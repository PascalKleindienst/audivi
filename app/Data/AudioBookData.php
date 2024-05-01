<?php

namespace App\Data;

use App\Models\AudioBook;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

/**
 * @property Lazy|Collection<int, AuthorData> $authors
 */
class AudioBookData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $path,
        public ?string $subtitle,
        public ?float $volume,
        public Lazy|string|null $description,
        public ?float $rating,
        public ?string $cover,
        public ?DateTime $published_at,
        public ?DateTime $created_at,
        public ?DateTime $updated_at,
        public Lazy|Collection $authors
    ) {
    }

    public function includeProperties(): array
    {
        return [
            'description',
        ];
    }

    public static function fromModel(AudioBook $book): self
    {
        return new self(
            $book->id,
            $book->title,
            $book->path,
            $book->subtitle,
            $book->volume,
            Lazy::create(static fn () => $book->description),
            $book->rating,
            $book->cover,
            $book->published_at,
            $book->created_at,
            $book->updated_at,
            Lazy::create(static fn () => AuthorData::collect($book->authors))
        );
    }
}
