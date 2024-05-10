<?php

declare(strict_types=1);

namespace App\Data\ID3;

use Spatie\LaravelData\Data;

final class TrackData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly int $position = 1,
        public readonly ?string $path = null
    ) {
    }
}
