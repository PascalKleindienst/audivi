<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Author;
use DateTime;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

final class AuthorData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public Lazy|null|string $image,
        public Lazy|null|string $description,
        public Lazy|null|string $link,
        public Lazy|null|DateTime $created_at,
        public Lazy|null|DateTime $updated_at
    ) {
    }

    public static function fromModel(Author $author): self
    {
        return new self(
            id: $author->id,
            name: $author->name,
            image: Lazy::create(static fn () => $author->image),
            description: Lazy::create(static fn () => $author->description),
            link: Lazy::create(static fn () => $author->link),
            created_at: Lazy::create(static fn () => $author->created_at),
            updated_at: Lazy::create(static fn () => $author->updated_at)
        );
    }
}
