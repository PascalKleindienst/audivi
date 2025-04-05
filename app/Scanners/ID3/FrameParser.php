<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\FrameData;
use App\Data\ID3\FrameType;
use App\Data\ID3\Genre;
use App\Data\ID3\ImageType;
use App\Data\ID3\ImageValueData;
use App\Scanners\ParserError;
use App\Utils\FileByteReader;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;
use Spatie\LaravelData\Data;

/**
 * @implements \App\Scanners\Parser<FrameData>
 */
final readonly class FrameParser implements \App\Scanners\Parser
{
    use FileByteReader;

    public function check(Version $version, Buffer $buffer): bool
    {
        return true;
    }

    public function parse(Buffer $buffer): Data
    {
        $header = [
            'id' => $this->getRaw($buffer, 4),
            'type' => $this->getRaw($buffer, 1),
            'size' => $this->getUint($buffer, 4, self::UINT32),
            'flags' => [$this->getUint($buffer, 8), $this->getUint($buffer, 9)],
        ];

        // No support for compressed, unsychronised, etc frames
        if ($header['flags'][1] !== 0) {
            throw new ParserError('Unsynchronised frames are not supported');
        }

        $matchedType = FrameType::from($header['id']->content);
        if ($matchedType === null) {
            throw new ParserError('Unsupported frame type: '.$header['id']);
        }

        // Get Frame Values for frame type
        $result = [];
        $result['value'] = null;
        $result['id'] = $header['id'];
        $result['type'] = $matchedType;

        if ($header['type']->content === 'T') {
            $result['value'] = $this->parseTextFrame($buffer, $header);
        } elseif ($header['type']->content === 'W') {
            $result['value'] = $this->getString($buffer, null, 10)->content;
        } elseif ($header['id']->content === 'APIC') {
            $result['value'] = $this->parsePicture($buffer);
        } elseif ($header['type']->content === 'C') {
            $result['value'] = $this->parseComment($buffer);
        }

        return FrameData::from($result);
    }

    /**
     * @param  array{id: Buffer, type: Buffer, size: int, flags: int[]}  $header
     */
    private function parseTextFrame(Buffer $buffer, array $header): string|Genre|null
    {
        $encoding = $this->getUint($buffer, 10);
        $val = match ($encoding) {
            0, 3 => $this->getString($buffer, null, 11)->convert(),
            1, 2 => $this->getRaw($buffer, null, 11 + 2)->convert(from: 'UTF-16LE'), // get utf-16 and convert to utf-8
            default => null
        };

        if ($header['id']->content === 'TCON' && \is_string($val)) {
            $val = Genre::tryFrom((int) trim($val, '()'));
        }

        return $val;
    }

    private function parseComment(Buffer $buffer): ?string
    {
        return match ($this->getUint($buffer, 10)) {
            0, 3 => $this->getString($buffer, null, 28)->convert(),
            1, 2 => $this->getRaw($buffer, null, 28 + 2)->convert(from: 'UTF-16LE'), // get utf-16 and convert to utf-8
            default => null
        };
    }

    private function parsePicture(Buffer $buffer): ImageValueData
    {
        $encoding = $this->getUint($buffer, 10);
        $bytes = 1;
        $charset = 'UTF8';
        if ($encoding === 1) {
            $bytes = 2;
            $charset = 'UTF16';
        }

        $variableStart = 11;
        $variableLength = $buffer->position(\chr(0), $variableStart) - $variableStart;
        $imageType = $this->getUint($buffer, $variableStart + $variableLength + 1);

        $image = [
            'type' => ImageType::tryFrom($imageType) ?? ImageType::OTHER,
            'mime' => $this->getString($buffer, $variableLength, $variableStart),
        ];

        $variableStart += $variableLength + (2 * $bytes);
        $variableLength = $buffer->position(mb_chr(0, $charset), $variableStart) - $variableStart;

        if ($variableLength !== 0) {
            $image['description'] = match ($encoding) {
                0, 3 => $this->getString($buffer, $variableLength, $variableStart),
                1 =>
                    // unsure if +1 or +bytes
                    $this->getRaw($buffer, $variableLength + 1, $variableStart)->convert(from: 'UTF-16LE'),
                default => null
            };
        }

        $image['data'] = $this->getRaw($buffer, null, $variableStart + $variableLength + $bytes + 1);

        return ImageValueData::from($image);
    }
}
