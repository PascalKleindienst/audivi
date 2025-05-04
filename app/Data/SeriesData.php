<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Series;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class SeriesData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('number')]
        public readonly ?int $id,
        public readonly string $name,
        /** @var Collection<int, AudioBookData> */
        public readonly Lazy|null|Collection $books = null,
        public readonly Lazy|null|DateTime $created_at = null,
        public readonly Lazy|null|DateTime $updated_at = null
    ) {}

    public static function fromModel(Series $series): self
    {
        return new self(
            id: $series->id,
            name: $series->name,
            books: Lazy::create(static fn () => AudioBookData::collect($series->audioBooks)),
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
