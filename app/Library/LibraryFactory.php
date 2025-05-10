<?php

declare(strict_types=1);

namespace App\Library;

use App\Actions\Library\SaveAudioBook;
use App\Data\AudioBookData;
use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Facades\Library;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use function count;

final class LibraryFactory
{
    public function __construct(
        private readonly SaveAudioBook $saveAudioBook
    ) {}

    /**
     * @param  MetaData[]  $items
     * @return AudioBookData[]
     */
    public function saveLibraryItem(array $items): array
    {
        return Arr::map($items, fn (MetaData $meta) => $this->createAudioBookFromMetadata($meta));
    }

    /**
     * @param  Collection<string>  $files
     */
    public function createLibraryItem(string $folder, Collection $files): ItemData
    {
        // TODO: Not entirly correct, check again
        $folder = str_replace(Storage::disk('library')->path(''), '', $folder); // use relative path
        $splitDir = array_filter(explode('/', $folder));

        // Audio files will always be in the directory named for the title
        $meta = [
            'title' => array_pop($splitDir) ?? '',
            'path' => $folder,
        ];

        // If there are at least 2 more directories, next furthest will be the series
        if (count($splitDir) > 1) {
            $meta['series'] = array_pop($splitDir);

            $series = Library::matchBySeries($meta['title']);
            if ($series) {
                $meta['title'] = $series['title'];
                $meta['volume'] = $series['volume'];
            }
        }

        if (count($splitDir) > 0) {
            $meta['authors'] = array_pop($splitDir);
        }

        // Get Published Date
        $publishedMatch = Library::matchByPublished($meta['title']);
        if ($publishedMatch) {
            $meta['published'] = $publishedMatch['published'];
            $meta['title'] = $publishedMatch['title'];
        }

        // TODO: Add option to not parse subtitle
        $subtitleMatch = Library::matchBySubtitle($meta['title']);
        if ($subtitleMatch) {
            $meta['title'] = $subtitleMatch['title'];
            $meta['subtitle'] = $subtitleMatch['subtitle'];
        }

        $volume = Library::matchByVolume($meta['title']);
        if ($volume) {
            $meta['title'] = $volume['title'];
            $meta['volume'] = $volume['volume'];
        }

        return ItemData::from(['folder' => $folder, 'files' => $files, 'meta' => $meta]);
    }

    private function createAudioBookFromMetadata(MetaData $metadata): AudioBookData
    {
        $data = $metadata->toArray();

        // save cover to public
        if ($metadata->cover && File::exists($metadata->cover)) {
            $data['cover'] = 'covers/'.hash('sha1', $metadata->cover).'.'.File::extension($metadata->cover);
            File::copy($metadata->cover, Storage::disk('public')->path($data['cover']));
        }

        return $this->saveAudioBook->save(AudioBookData::from($data));
    }
}
