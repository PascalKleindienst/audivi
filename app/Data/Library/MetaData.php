<?php

declare(strict_types=1);

namespace App\Data\Library;

use App\Data\TrackData;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

final class MetaData extends Data implements \Stringable
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
    ) {}

    public function __toString(): string
    {
        $title = $this->title;

        if ($this->subtitle) {
            $title .= " - {$this->subtitle}";
        }

        if ($this->series) {
            $title .= " [{$this->series}]";
        }

        return $title ?? 'UNKNOWN';
    }
}
