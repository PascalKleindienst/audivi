<?php

declare(strict_types=1);

namespace App\Actions\Library;

use App\Data\AudioBookData;
use App\Data\AuthorData;
use App\Models\AudioBook;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Series;
use App\Models\Track;
use Illuminate\Support\Collection;

final readonly class SaveAudioBookAction
{
    private const MAX_LEVENSHTEIN_DISTANCE = 4;

    public function handle(AudioBookData $data): AudioBookData
    {
        // Upsert Audiobook -> action
        $book = AudioBook::query()
            // ->with(['series', 'tracks', 'authors', 'publisher'])
            ->updateOrCreate(['path' => $data->path], [
                'title' => $data->title,
                'subtitle' => $data->subtitle,
                'volume' => $data->volume,
                'description' => $data->description,
                'rating' => $data->rating,
                'cover' => $data->cover,
                'published_at' => $data->published_at,
                'path' => $data->path,
                'duration' => $data->duration,
            ]);

        // save series
        if ($data->series) {
            $series = Series::updateOrCreate(['name' => $data->series->name], [
                'name' => $data->series->name,
            ]);
            $book->series_id = $series->id;
            $book->save();
        }

        // Save publisher
        if ($data->publisher) {
            $publisher = Publisher::updateOrCreate(['name' => $data->publisher->name], [
                'name' => $data->publisher->name,
            ]);
            $book->publisher_id = $publisher->id;
            $book->save();
        }

        // save tracks
        if (! empty($data->tracks) && $data->tracks instanceof Collection) {
            foreach ($data->tracks as $track) {
                Track::updateOrCreate(
                    ['audio_book_id' => $book->id, 'position' => $track->position],
                    [
                        'title' => $track->title,
                        'path' => $track->path,
                        'start' => $track->start,
                        'end' => $track->end,
                        'duration' => $track->duration,
                        'position' => $track->position,
                        'mTime' => $track->mTime,
                        'audio_book_id' => $book->id,
                    ]
                );
            }
        }

        // save / sync authors
        if (! empty($data->authors)) {
            $newAuthors = $data->authors
                ->map(static fn (AuthorData $author) => ['name' => $author->name])
                ->where(static fn (array $author) => $book->authors
                    ->filter(static fn (Author $item) => levenshtein($item->name, $author['name']) <= self::MAX_LEVENSHTEIN_DISTANCE)
                    ->isEmpty()
                );

            // Save new authors, load author ids and sync them with the book
            $book->authors()->upsert($newAuthors->toArray(), ['name']);
            $authors = Author::query()->whereIn('name', $data->authors->pluck('name'))->get();
            $book->authors()->sync($authors);
        }

        return AudioBookData::from($book->refresh());
    }
}
