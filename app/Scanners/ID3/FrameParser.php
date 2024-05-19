<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\FrameData;
use App\Data\ID3\FrameType;
use App\Data\ID3\Genre;
use App\Data\ID3\ImageType;
use App\Data\ID3\ImageValueData;
use App\Utils\FileByteReader;

final class FrameParser
{
    use FileByteReader;

    public function parse(string $buffer): ?FrameData
    {
        $header = [
            'id' => $this->getRaw($buffer, 4),
            'type' => $this->getRaw($buffer, 1),
            'size' => $this->getUint($buffer, 4, self::UINT32),
            'flags' => [$this->getUint($buffer, 8), $this->getUint($buffer, 9)],
        ];

        // No support for compressed, unsychronised, etc frames
        if ($header['flags'][1] !== 0) {
            return null;
        }

        $matchedType = FrameType::from($header['id']);
        if ($matchedType === null) {
            return null;
        }

        // Get Frame Values for frame type
        $result = [];
        $result['value'] = null;
        $result['id'] = $header['id'];
        $result['type'] = $matchedType;

        if ($header['type'] === 'T') {
            $result['value'] = $this->parseTextFrame($buffer, $header);
        } elseif ($header['type'] === 'W') {
            $result['value'] = $this->getString($buffer, null, 10);
        } elseif ($header['id'] === 'APIC') {
            $result['value'] = $this->parsePicture($buffer);
        } elseif ($header['type'] === 'C') {
            $result['value'] = $this->parseComment($buffer);
        }

        return FrameData::from($result);
    }

    /**
     * @param  array{id: string, type: string, size: int, flags: int[]}  $header
     */
    private function parseTextFrame(string $buffer, array $header): string|Genre|null
    {
        $encoding = $this->getUint($buffer, 10);
        $val = match ($encoding) {
            0, 3 => mb_convert_encoding($this->getString($buffer, null, 11), 'UTF-8', 'ISO-8859-1'),
            1, 2 => mb_convert_encoding($this->getRaw($buffer, null, 11 + 2), 'UTF-8', 'UTF-16LE'), // get utf-16 and convert to utf-8
            default => null
        };

        if ($header['id'] === 'TCON' && \is_string($val)) {
            $val = Genre::tryFrom((int) trim($val, '()'));
        }

        return $val;
    }

    private function parseComment(string $buffer): ?string
    {
        return match ($this->getUint($buffer, 10)) {
            0, 3 => mb_convert_encoding($this->getString($buffer, null, 28), 'UTF-8', 'ISO-8859-1'),
            1, 2 => mb_convert_encoding($this->getRaw($buffer, null, 28 + 2), 'UTF-8', 'UTF-16LE'), // get utf-16 and convert to utf-8
            default => null
        };
    }

    private function parsePicture(string $buffer): ImageValueData
    {
        $encoding = $this->getUint($buffer, 10);
        $bytes = 1;
        $charset = 'UTF8';
        if ($encoding === 1) {
            $bytes = 2;
            $charset = 'UTF16';
        }

        $variableStart = 11;
        $variableLength = (strpos($buffer, \chr(0), $variableStart) ?: 0) - $variableStart;
        $imageType = $this->getUint($buffer, $variableStart + $variableLength + 1);

        $image = [
            'type' => ImageType::tryFrom($imageType) ?? ImageType::OTHER,
            'mime' => $this->getString($buffer, $variableLength, $variableStart),
        ];

        $variableStart += $variableLength + (2 * $bytes);
        $variableLength = (strpos($buffer, mb_chr(0, $charset), $variableStart) ?: 0) - $variableStart;

        if ($variableLength !== 0) {
            $image['description'] = match ($encoding) {
                0, 3 => $this->getString($buffer, $variableLength, $variableStart),
                1 => mb_convert_encoding(
                    // unsure if +1 or +bytes
                    $this->getRaw($buffer, $variableLength + 1, $variableStart),
                    'UTF-8',
                    'UTF-16LE'
                ),
                default => null
            };
        }

        $image['data'] = $this->getRaw($buffer, null, $variableStart + $variableLength + $bytes + 1);

        return ImageValueData::from($image);
    }
}
