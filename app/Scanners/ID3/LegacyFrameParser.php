<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\FrameData;
use App\Data\ID3\FrameType;
use App\Data\ID3\Genre;
use App\Data\ID3\ImageType;
use App\Data\ID3\ImageValueData;
use App\Utils\FileByteReader;

final class LegacyFrameParser
{
    use FileByteReader;

    public function check(int $majorVersion): bool
    {
        return $majorVersion < 3;
    }

    public function parse(string $buffer): ?FrameData
    {
        $header = [
            'id' => $this->getRaw($buffer, 3),
            'type' => $this->getRaw($buffer, 1),
            'size' => $this->getUint($buffer, 3, self::UINT24),
        ];

        $matchedType = FrameType::from($header['id']);
        if ($matchedType === null) {
            return null;
        }

        // Get Frame Values for frame type
        $result = [];
        $result['id'] = $header['id'];
        $result['type'] = $matchedType;

        if ($header['type'] === 'T') {
            $value = $this->getString($buffer, null, 7);
            $genre = (int) trim($value, '()');

            if ($header['id'] === 'TCO') {
                $value = Genre::tryFrom($genre);
            }

            $result['value'] = $value;
        } elseif ($header['type'] === 'W') {
            $result['value'] = $this->getString($buffer, null, 7);
        } elseif ($header['id'] === 'PIC') {
            $variableStart = 11;
            $variableLength = (strpos($buffer, \chr(0), $variableStart) ?: 0) - $variableStart;
            $imageType = $this->getUint($buffer, 11);

            $result['value'] = ImageValueData::from([
                'type' => ImageType::tryFrom($imageType) ?? ImageType::OTHER,
                'mime' => 'image/'.strtolower($this->getString($buffer, 3, 7)),
                'description' => $variableLength === 0
                    ? null
                    : $this->getString($buffer, $variableLength, $variableStart),
                'data' => $this->getRaw($buffer, null, $variableStart + $variableLength + 1),
            ]);
        }

        return FrameData::from($result);
    }
}
