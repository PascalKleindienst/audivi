<?php

declare(strict_types=1);

namespace App\Data\ID3;

use App\Enums\ID3\Genre;
use App\ValueObjects\Version;
use Spatie\LaravelData\Data;

final class TagData extends Data
{
    public function __construct(
        public readonly ?string $kind = null,
        public readonly ?string $title = null,
        public readonly ?string $album = null,
        public readonly ?string $artist = null,
        public readonly ?int $year = null,
        public readonly ?Version $version = null,
        public readonly ?string $comments = null,
        public readonly ?string $track = null,
        public readonly ?Genre $genre = null,
        public readonly ?string $publisher = null,
        public readonly ?string $language = null,
        /** @var FrameData[] */
        public readonly array $frames = [],
        /** @var ImageValueData[] */
        public readonly array $images = []
    ) {}
}
