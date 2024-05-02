<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\AudioBook;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

/**
 * @property Lazy|Collection<int, AuthorData> $authors
 */
final class AudioBookData extends Data
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
        public DateTime|Carbon|null $published_at,
        public DateTime|Carbon|null $created_at,
        public DateTime|Carbon|null $updated_at,
        public Lazy|Collection $authors
    ) {
    }

    /**
     * @return string[]
     */
    public function includeProperties(): array
    {
        return [
            'description',
        ];
    }

    public static function fromModel(AudioBook $book): self
    {
        return new self(
            id: $book->id,
            title: $book->title,
            path: $book->path,
            subtitle: $book->subtitle,
            volume: $book->volume,
            description: Lazy::create(static fn () => $book->description),
            rating: $book->rating,
            cover: $book->cover,
            published_at: $book->published_at,
            created_at: $book->created_at,
            updated_at: $book->updated_at,
            authors: Lazy::create(static fn () => AuthorData::collect($book->authors))
        );
    }
}
