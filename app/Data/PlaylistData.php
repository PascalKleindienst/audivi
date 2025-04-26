<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\AudioBook;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Data;

class PlaylistData extends Data
{
    public function __construct(
        public readonly int $bookId,
        public readonly string $title,
        public readonly ?string $cover,
        /** @var Collection<int, PlaylistTrackData> */
        public readonly Collection $tracks
    ) {}

    public static function fromModel(AudioBook $book): self
    {
        return new self(
            bookId: $book->id,
            title: $book->title,
            cover: $book->cover ? Storage::disk('public')->url($book->cover) : null,
            tracks: PlaylistTrackData::collect($book->tracks),
        );
    }
}
