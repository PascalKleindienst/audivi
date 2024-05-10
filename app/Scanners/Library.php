<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Actions\Library\SaveAudioBook;
use App\Data\AudioBookData;
use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

final class Library
{
    private const SERIES_PATTERN = "/^#?(\d{1,3}(?:\.\d{1,2})?)\s?-\s?/";

    private const VOLUME_PATTERN = "/[-|,]?\s?\b(?:Book|Vol.?|Volume|EP) (\d{0,3}(?:\.\d{1,2})?)\b\s?[-|,]?\s?/i";

    private const PUBLISHED_PATTERN = "/^(\(?\d{4}\)?) - (.+)/sm";

    public function __construct(
        private readonly SaveAudioBook $saveAudioBook
    ) {
    }

    /**
     * @param  MetaData[]  $items
     * @return AudioBookData[]
     */
    public function saveLibraryItem(array $items): array
    {
        return Arr::map($items, fn (MetaData $meta) => $this->createAudioBookFromMetadata($meta));
    }

    private function createAudioBookFromMetadata(MetaData $metadata): AudioBookData
    {
        $data = $metadata->toArray();

        // save cover to public
        if ($metadata->cover && File::exists($metadata->cover)) {
            $data['cover'] = 'covers/'.sha1($metadata->cover).'.'.File::extension($metadata->cover);
            File::copy($metadata->cover, Storage::path('public/'.$data['cover']));
        }

        return $this->saveAudioBook->save(AudioBookData::from($data));
    }

    /**
     * @param  Collection<SplFileInfo>  $files
     */
    public function createLibraryItem(string $folder, Collection $files): ItemData
    {
        // TODO: Not entirly correct, check again
        $folder = str_replace(Storage::path('library'), '', $folder); // use relative path
        $splitDir = array_filter(explode('/', $folder));

        // Audio files will always be in the directory named for the title
        $meta = [
            'title' => array_pop($splitDir) ?? '',
            'path' => $folder,
        ];

        // If there are at least 2 more directories, next furthest will be the series
        if (\count($splitDir) > 1) {
            $meta['series'] = array_pop($splitDir);

            // Match Titles which starts with a number (max digits), which can have 2 decimal places,
            // and use it as the vol number, e.g. #1, 12, 12.1, 100.12
            if (preg_match(self::SERIES_PATTERN, $meta['title'], $volume)) {
                $meta['title'] = str_replace($volume[0], '', $meta['title']);
                $meta['volume'] = (int) $volume[1];
            }

            // Match volume by Book #, Vol. #, Volume #, or EP #
            if (preg_match(self::VOLUME_PATTERN, $meta['title'], $volume)) {
                $meta['title'] = str_replace($volume[0], '', $meta['title']);
                $meta['volume'] = (int) $volume[1];
            }
        }

        if (\count($splitDir) > 0) {
            $meta['authors'] = array_pop($splitDir);
        }

        // Get Published Date
        if (preg_match(self::PUBLISHED_PATTERN, $meta['title'], $publishedMatch)) {
            $meta['published'] =
                \DateTime::createFromFormat('Y', trim($publishedMatch[1], '()')) ?: null;
            $meta['title'] = trim($publishedMatch[2]);
        }

        // TODO: Add option to not parse subtitle
        if (str_contains($meta['title'], ' - ')) {
            $titleParts = explode(' - ', $meta['title']);
            $meta['title'] = array_shift($titleParts);
            $meta['subtitle'] = implode(' - ', $titleParts);
        }

        return ItemData::from(['folder' => $folder, 'files' => $files, 'meta' => $meta]);
    }
}
