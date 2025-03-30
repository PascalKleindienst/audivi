<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\Genre;
use App\Data\ID3\TagData;
use App\Scanners\Parser;
use App\Utils\FileByteReader;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;
use Spatie\LaravelData\Data;

/**
 * @implements Parser<TagData>
 */
final readonly class LegacyTagParser implements Parser
{
    use FileByteReader;

    public function check(Version $version, Buffer $buffer): bool
    {
        // Read the last 128 bytes (ID3v1)
        // Specification: https://id3.org/ID3v1
        $headerBuffer = $this->getRaw($buffer, 128, -128);

        return \strlen($headerBuffer->content) === 128 && $this->getString($headerBuffer, 3)->content === 'TAG';
    }

    public function parse(Buffer $buffer): Data
    {
        $data = [
            'kind' => 'v1',
            'title' => trim($this->getString($buffer, 30, 3)->convert()) ?: null,
            'artist' => trim($this->getString($buffer, 30, 33)->convert()) ?: null,
            'album' => trim($this->getString($buffer, 30, 63)->convert()) ?: null,
            'year' => (int) trim($this->getString($buffer, 4, 93)->content) ?: null,
            'comments' => $this->getString($buffer, 30, 97)->convert() ?: null,
            'genre' => Genre::tryFrom($this->getUint($buffer, 127)),
        ];

        // If there is a zero byte at [125], the comment is 28 bytes and the remaining 2 are [0, track]
        if ($this->getUint($buffer, 125) === 0) {
            $data['comments'] = $this->getString($buffer, 28, 97)->convert();
            $data['version'] = Version::from(1, 1);
            $data['track'] = (string) $this->getUint($buffer, 126);
        }

        return TagData::from($data);
    }
}
