<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\FrameData;
use App\Data\ID3\ImageValueData;
use App\Enums\ID3\FrameType;
use App\Enums\ID3\Genre;
use App\Enums\ID3\ImageType;
use App\Exceptions\ParserError;
use App\Utils\FileByteReader;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;

use function chr;

/**
 * @implements \App\Scanners\Parser<FrameData>
 */
final readonly class LegacyFrameParser implements \App\Scanners\Parser
{
    use FileByteReader;

    public function check(Version $version, Buffer $buffer): bool
    {
        return $version->major < 3;
    }

    public function parse(Buffer $buffer): FrameData
    {
        $header = [
            'id' => $this->getRaw($buffer, 3),
            'type' => $this->getRaw($buffer, 1),
            'size' => $this->getUint($buffer, 3, self::UINT24),
        ];

        $matchedType = FrameType::from($header['id']->content);
        if ($matchedType === null) {
            throw new ParserError('Unsupported frame type: '.$header['id']);
        }

        // Get Frame Values for frame type
        $result = [];
        $result['id'] = $header['id'];
        $result['type'] = $matchedType;

        if ($header['type']->content === 'T') {
            $value = $this->getString($buffer, null, 7);
            $genre = (int) trim($value->content, '()');

            if ($header['id']->content === 'TCO') {
                $value = Genre::tryFrom($genre);
            }

            $result['value'] = $value;
        } elseif ($header['type']->content === 'W') {
            $result['value'] = $this->getString($buffer, null, 7);
        } elseif ($header['id']->content === 'PIC') {
            $variableStart = 11;
            $variableLength = $buffer->position(chr(0), $variableStart) - $variableStart;
            $imageType = $this->getUint($buffer, 11);

            $result['value'] = ImageValueData::from([
                'type' => ImageType::tryFrom($imageType) ?? ImageType::OTHER,
                'mime' => 'image/'.strtolower($this->getString($buffer, 3, 7)->content),
                'description' => $variableLength === 0
                    ? null
                    : $this->getString($buffer, $variableLength, $variableStart),
                'data' => $this->getRaw($buffer, null, $variableStart + $variableLength + 1),
            ]);
        }

        return FrameData::from($result);
    }
}
