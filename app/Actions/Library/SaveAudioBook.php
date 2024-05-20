<?php

declare(strict_types=1);

namespace App\Actions\Library;

use App\Data\AudioBookData;
use App\Data\AuthorData;
use App\Data\TrackData;
use App\Models\AudioBook;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Series;
use App\Models\Track;
use Illuminate\Support\Collection;

final class SaveAudioBook
{
    private const MAX_LEVENSHTEIN_DISTANCE = 4;

    public function save(AudioBookData $data): AudioBookData
    {
        // Upsert Audiobook -> action
        $book = AudioBook::query()
            ->with(['series', 'tracks', 'authors', 'publisher'])
            ->updateOrCreate(['path' => $data->path], $data->toArray());

        // save series
        if ($data->series) {
            $series = Series::updateOrCreate(['name' => $data->series->name], $data->series->toArray());
            $book->series_id = $series->id;
            $book->save();
        }

        // Save publisher
        if ($data->publisher) {
            $publisher = Publisher::updateOrCreate(['name' => $data->publisher->name], $data->publisher->toArray());
            $book->publisher_id = $publisher->id;
            $book->save();
        }

        // save tracks
        if (! empty($data->tracks) && $data->tracks instanceof Collection) {
            $newTracks = $data->tracks->map(static fn (TrackData $track) => new Track($track->toArray()))
                ->where(
                    static fn (Track $track) => $book->tracks
                        ->filter(
                            static fn (Track $item) => $item->path === $track->path && $item->position === $track->position
                        )
                        ->isEmpty()
                )
                ->each(static fn (Track $track) => $track->audio_book_id = $book->id);

            $book->tracks()->upsert($newTracks->toArray(), ['position', 'path']);
        }

        // save / sync authors
        if (! empty($data->authors) && $data->authors instanceof Collection) {
            $newAuthors = $data->authors->map(static fn (AuthorData $author) => new Author($author->toArray()))
                ->where(
                    static fn (Author $author) => $book->authors
                        ->filter(
                            static fn (Author $item) => levenshtein($item->name, $author->name) <= self::MAX_LEVENSHTEIN_DISTANCE
                        )
                        ->isEmpty()
                );

            // Save new authors, load author ids and sync them with the book
            $book->authors()->upsert($newAuthors->toArray(), ['name'], ['name']);
            $authors = Author::query()->whereIn('name', $data->authors->pluck('name'))->get();
            $book->authors()->sync($authors);
        }

        return AudioBookData::from($book->refresh());
    }
}
