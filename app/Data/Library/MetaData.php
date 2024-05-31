<?php

declare(strict_types=1);

namespace App\Data\Library;

use App\Data\ID3\TrackData;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

final class MetaData extends Data
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $subtitle,
        public readonly ?string $series,
        public readonly ?int $volume,
        public readonly ?string $description,
        public readonly ?string $publisher,
        public readonly ?\DateTime $published_at = null,
        public readonly ?string $cover = null,
        public readonly ?string $language = null,
        public readonly string $path = '.',
        public readonly ?int $duration = null,
        /** @var string[] */
        #[WithCast(AuthorCast::class)]
        public readonly array $authors = [],
        /** @var TrackData[] */
        public readonly array $tracks = []
    ) {
    }
}
