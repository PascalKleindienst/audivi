<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\Genre;
use App\Data\ID3\TagData;
use App\Utils\FileByteReader;

final class LegacyTagParser
{
    use FileByteReader;

    public function check(string $buffer): bool
    {
        // Read the last 128 bytes (ID3v1)
        // Specification: https://id3.org/ID3v1
        $headerBuffer = $this->getRaw($buffer, 128, -128);

        return \strlen($headerBuffer) === 128 && $this->getString($headerBuffer, 3) === 'TAG';
    }

    public function parse(string $buffer): TagData
    {
        $data = [
            'kind' => 'v1',
            'title' => trim($this->getString($buffer, 30, 3)) ?: null,
            'artist' => trim($this->getString($buffer, 30, 33)) ?: null,
            'album' => trim($this->getString($buffer, 30, 63)) ?: null,
            'year' => (int) trim($this->getString($buffer, 4, 93)) ?: null,
            'comments' => $this->getString($buffer, 30, 97) ?: null,
            'genre' => Genre::tryFrom($this->getUint($buffer, 127)),
        ];

        // If there is a zero byte at [125], the comment is 28 bytes and the remaining 2 are [0, track]
        if ($this->getUint($buffer, 125) === 0) {
            $data['comments'] = $this->getString($buffer, 28, 97);
            $data['version'] = 1.1;
            $data['track'] = (string) $this->getUint($buffer, 126);
        }

        return TagData::from($data);
    }
}
