<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Series;
use DateTime;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

final class SeriesData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly Lazy|null|DateTime $created_at = null,
        public readonly Lazy|null|DateTime $updated_at = null
    ) {
    }

    public static function fromModel(Series $series): self
    {
        return new self(
            id: $series->id,
            name: $series->name,
            created_at: Lazy::create(static fn () => $series->created_at),
            updated_at: Lazy::create(static fn () => $series->updated_at)
        );
    }

    public static function fromString(string $name): self
    {
        return new self(
            id: null,
            name: $name,
            created_at: null,
            updated_at: null
        );
    }
}
