<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\ImageValueData;
use App\Data\ID3\TagData;
use App\Enums\ID3\FrameType;
use App\Exceptions\ParserError;
use App\Utils\FileByteReader;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;
use Spatie\LaravelData\Data;

/**
 * @implements \App\Scanners\Parser<TagData>
 */
final readonly class TagParser implements \App\Scanners\Parser
{
    use FileByteReader;

    /**
     * Make sure that the buffer is at least the size of an id3v2 header
     * Specification: https://id3.org/id3v2.3.0
     */
    public function check(Version $version, Buffer $buffer): bool
    {
        $prefixBuffer = $this->getRaw($buffer, 14);

        return $prefixBuffer->length === 14 && $this->getRaw($prefixBuffer, 3)->content === 'ID3'
            && $this->getUint($buffer, 3) <= 4;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function parse(Buffer $buffer, array $data = []): Data
    {
        $prefixBuffer = $this->getRaw($buffer, 14);
        $tagFlags = $this->getUint($prefixBuffer, 5);

        // Do not support unsynchronisation
        if (($tagFlags & 0x80) !== 0) {
            throw new ParserError('Unsynchronised frames are not supported');
        }

        $headerSize = 10;
        $tagSize = 10;
        $version = Version::from($this->getUint($buffer, 3), $this->getUint($buffer, 4));

        $data['kind'] = 'v2';
        $data['version'] = $version;

        // Increment the header size to account for an extended header
        if (($tagFlags & 0x40) !== 0) {
            $headerSize += $this->synch($this->getUint($prefixBuffer, 11, self::UINT32));
        }

        // Calculate the tag size to be read
        $tagSize += $this->synch($this->getUint($prefixBuffer, 6, self::UINT32));
        $v2TagBuffer = $this->getRaw($buffer, $tagSize, $headerSize);
        $position = 0;

        // Get frames from the buffer
        while ($position < $v2TagBuffer->length) {
            if (! Parser::isFrame($v2TagBuffer, $position)) {
                break;
            }

            // Parse the extracted frome and add it to the tag
            $slice = $this->getFrameSlice($v2TagBuffer, $version, $position);

            try {
                $frame = Parser::parseFrame($slice, $version);
                $data['frames'][] = $frame;

                if ($frame->type === FrameType::IMAGE && $frame->value instanceof ImageValueData) {
                    $data['images'][] = $frame->value;
                } else {
                    $data[strtolower($frame->type->name)] = $frame->value;
                }
            } catch (ParserError) {
            }

            $position += $slice->length;
        }

        return TagData::from($data);
    }

    /**
     * If version < 2.3 => Frame ID is 3 chars, size is 3 bytes (uint24) => total 6 bytes
     * If version >= 2.3 => Frame ID is 4 chars, size is 4 bytes (uint32), flags are 2 bytes
     * => total 10 bytes
     */
    private function getFrameSlice(Buffer $buffer, Version $version, int $position): Buffer
    {
        return match ($version->major) {
            1, 2 => $this->getRaw(
                $buffer,
                6 + $this->getUint($buffer, $position + 3, self::UINT24),
                $position
            ),
            3 => $this->getRaw(
                $buffer,
                10 + $this->getUint($buffer, $position + 4, self::UINT32),
                $position
            ),
            default => $this->getRaw(
                $buffer,
                10 + $this->synch($this->getUint($buffer, $position + 4, self::UINT32)),
                $position
            ),
        };
    }
}
