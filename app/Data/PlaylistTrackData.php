<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Track;
use Spatie\LaravelData\Data;

class PlaylistTrackData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly int $position,
        public readonly string $src,
        public readonly float $start,
        public readonly float $end,
        public readonly int $duration,
        public readonly int $currentTime = 0
    ) {}

    public static function fromModel(Track $track): self
    {
        return new self(
            id: $track->id,
            title: $track->title,
            position: $track->position,
            src: route('playlist.play', $track->id),
            start: $track->start ?? 0.0,
            end: $track->end ?? (float) $track->duration,
            duration: $track->duration ?? (int) ($track->end ?? 0),
        );
    }
}
