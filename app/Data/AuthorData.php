<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Author;
use DateTime;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class AuthorData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('number')] // ID is only null when scanning new entries and never on the frontend
        public readonly ?int $id,
        public readonly string $name,
        public readonly Lazy|null|string|UploadedFile $image,
        public readonly Lazy|null|string $description,
        public readonly Lazy|null|string $link,
        public readonly Lazy|null|DateTime $created_at = null,
        public readonly Lazy|null|DateTime $updated_at = null
    ) {}

    /**
     * @return array<string, array<int, string>>
     */
    public static function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
        ];
    }

    public static function fromModel(Author $author): self
    {
        return new self(
            id: $author->id,
            name: $author->name,
            image: Lazy::create(static fn () => $author->image ? Storage::disk('public')->url('authors/'.$author->image) : null),
            description: Lazy::create(static fn () => $author->description),
            link: Lazy::create(static fn () => $author->link),
            created_at: Lazy::create(static fn () => $author->created_at),
            updated_at: Lazy::create(static fn () => $author->updated_at)
        );
    }

    public static function fromString(string $name): self
    {
        return new self(
            id: null,
            name: $name,
            image: null,
            description: null,
            link: null,
            created_at: null,
            updated_at: null
        );
    }
}
