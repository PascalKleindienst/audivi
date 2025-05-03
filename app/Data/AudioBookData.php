<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\Library\MetaData;
use App\Models\AudioBook;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class AudioBookData extends Data
{
    public readonly ?int $duration;

    #[Computed]
    public readonly ?float $fileSize;

    public function __construct(
        #[LiteralTypeScriptType('number')] // ID is only null when scanning new entries and never on the frontend
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $path,
        public readonly ?string $subtitle,
        public readonly ?float $volume,
        public readonly Lazy|string|null $description,
        public readonly ?float $rating,
        public readonly ?string $cover, // TODO: Absolute URL Transform?
        public readonly ?string $language,
        ?int $duration,
        /** @var Collection<int, AuthorData> */
        public readonly Lazy|Collection $authors,
        /** @var Collection<int, TrackData> */
        public readonly Lazy|Collection $tracks,
        public readonly Lazy|SeriesData|null $series = null,
        public readonly Lazy|PublisherData|null $publisher = null,
        public readonly DateTime|Carbon|null $published_at = null,
        public readonly DateTime|Carbon|null $created_at = null,
        public readonly DateTime|Carbon|null $updated_at = null,
    ) {
        /** @var Collection<int, TrackData> $tmpTracks */
        $tmpTracks = $this->tracks instanceof Lazy ? $this->tracks->resolve() : $this->tracks;

        if ($duration === null && $tmpTracks->isNotEmpty()) {
            $this->duration = $tmpTracks->sum(static fn (TrackData $track) => $track->duration);
        } else {
            $this->duration = $duration;
        }

        if ($tmpTracks->isNotEmpty()) {
            $this->fileSize = $tmpTracks
                ->map(static fn (TrackData $track) => $track->path)
                ->unique()
                ->filter(static fn (?string $trackPath) => Storage::disk('library')->exists($path.'/'.$trackPath))
                ->sum(static fn (?string $trackPath) => Storage::disk('library')->size($path.'/'.$trackPath));
        } else {
            $this->fileSize = null;
        }
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
            cover: $book->cover ? Storage::disk('public')->url($book->cover) : null,
            language: $book->language,
            duration: $book->duration,
            authors: Lazy::create(static fn () => AuthorData::collect($book->authors)),
            tracks: Lazy::create(static fn () => TrackData::collect($book->tracks)),
            series: Lazy::create(static fn () => $book->series ? SeriesData::from($book->series) : null),
            publisher: Lazy::create(static fn () => $book->publisher ? PublisherData::from($book->publisher) : null),
            published_at: $book->published_at,
            created_at: $book->created_at,
            updated_at: $book->updated_at,
        );
    }

    public static function fromMetaData(MetaData $metadata): self
    {
        return new self(
            id: null,
            title: $metadata->title ?? 'UNKNOWN',
            path: $metadata->path,
            subtitle: $metadata->subtitle,
            volume: $metadata->volume,
            description: $metadata->description,
            rating: null,
            cover: $metadata->cover,
            language: $metadata->language,
            duration: $metadata->duration,
            authors: AuthorData::collect($metadata->authors, Collection::class),
            tracks: TrackData::collect($metadata->tracks, Collection::class),
            series: SeriesData::from($metadata->series),
            publisher: PublisherData::from($metadata->publisher),
            published_at: $metadata->published_at,
        );
    }
}
