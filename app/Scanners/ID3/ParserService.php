<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\FrameData;
use App\Data\ID3\TagData;
use App\Exceptions\ParserError;
use App\Utils\FileByteReader;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;

use function ord;

final readonly class ParserService
{
    use FileByteReader;

    public function __construct(
        private TagParser $tagParser,
        private FrameParser $frameParser,
        private LegacyTagParser $legacyTagParser,
        private LegacyFrameParser $legacyFrameParser,
    ) {}

    /**
     * @throws ParserError
     */
    public function parseTag(Buffer $buffer, Version $version): ?TagData
    {
        $data = null;

        if ($this->legacyTagParser->check($version, $buffer)) {
            $data = $this->legacyTagParser->parse($buffer);
        }

        if ($this->tagParser->check($version, $buffer)) {
            // $this->tagParser->setDefault($data?->toArray() ?? []);
            $data = $this->tagParser->parse($buffer, $data?->toArray() ?? []);
        }

        return $data;
    }

    /**
     * @throws ParserError
     */
    public function parseFrame(Buffer $buffer, Version $version): FrameData
    {
        // Parse Legacy (< id3v2.3)
        if ($this->legacyFrameParser->check($version, $buffer)) {
            return $this->legacyFrameParser->parse($buffer);
        }

        return $this->frameParser->parse($buffer);
    }

    public function isFrame(Buffer $buffer, int $position): bool
    {
        for ($i = 0; $i < 3; $i++) {
            $frameBit = $this->getUint($buffer, $position + $i);

            // If the framebit is not part of [A-Z0-9] its not the start of a frame
            if (
                max(min($frameBit, ord('Z')), ord('A')) !== $frameBit &&
                max(min($frameBit, ord('9')), ord('0')) !== $frameBit
            ) {
                return false;
            }
        }

        return true;
    }
}
