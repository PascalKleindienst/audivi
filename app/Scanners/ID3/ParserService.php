<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\FrameData;
use App\Data\ID3\TagData;
use App\Utils\FileByteReader;

final class ParserService
{
    use FileByteReader;

    public function __construct(
        private readonly TagParser $tagParser,
        private readonly FrameParser $frameParser,
        private readonly LegacyTagParser $legacyTagParser,
        private readonly LegacyFrameParser $legacyFrameParser,
    ) {
    }

    public function parseTag(string $buffer): ?TagData
    {
        $data = null;

        if ($this->legacyTagParser->check($buffer)) {
            $data = $this->legacyTagParser->parse($buffer);
        }

        if ($this->tagParser->check($buffer)) {
            $data = $this->tagParser->parse($buffer, $data?->toArray() ?? []);
        }

        return $data;
    }

    public function parseFrame(string $buffer, int $majorVersion): ?FrameData
    {
        // Parse Legacy (< id3v2.3)
        if ($this->legacyFrameParser->check($majorVersion)) {
            return $this->legacyFrameParser->parse($buffer);
        }

        return $this->frameParser->parse($buffer);
    }

    public function isFrame(string $buffer, int $position): bool
    {
        for ($i = 0; $i < 3; $i++) {
            $frameBit = $this->getUint($buffer, $position + $i);

            // If the framebit is not part of [A-Z0-9] its not the start of a frame
            if (
                max(min($frameBit, \ord('Z')), \ord('A')) !== $frameBit &&
                max(min($frameBit, \ord('9')), \ord('0')) !== $frameBit
            ) {
                return false;
            }
        }

        return true;
    }
}
