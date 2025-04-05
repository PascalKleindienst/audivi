<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;

final class LibraryService
{
    public const SERIES_PATTERN = "/^#?(\d{1,3}(?:\.\d{1,2})?)\s?-\s?/";

    public const VOLUME_PATTERN = "/[-|,]?\s?\b(?:Book|Vol.?|Volume|EP) (\d{0,3}(?:\.\d{1,2})?)\b\s?[-|,]?\s?/i";

    public const PUBLISHED_PATTERN = "/^(\(?\d{4}\)?) - (.+)/sm";

    /**
     * Match Titles which starts with a number (max digits), which can have 2 decimal places,
     * and use it as the vol number, e.g. #1, 12, 12.1, 100.12
     *
     * @return array{title: string, volume: int}|null
     */
    public function matchBySeries(string $title): ?array
    {
        if (preg_match(self::SERIES_PATTERN, $title, $volume)) {
            return [
                'title' => str_replace($volume[0], '', $title),
                'volume' => (int) $volume[1],
            ];
        }

        return null;
    }

    /**
     * Match volume by Book #, Vol. #, Volume #, or EP #
     *
     * @return array{title: string, volume: int}|null
     */
    public function matchByVolume(string $title): ?array
    {
        if (preg_match(self::VOLUME_PATTERN, $title, $volume)) {
            return [
                'title' => str_replace($volume[0], '', $title),
                'volume' => (int) $volume[1],
            ];
        }

        return null;
    }

    /**
     * Get Published data from title
     *
     * @return array{title: string, published: DateTime|null}|null
     */
    public function matchByPublished(string $title): ?array
    {
        if (preg_match(self::PUBLISHED_PATTERN, $title, $publishedMatch)) {
            return [
                'title' => trim($publishedMatch[2]),
                'published' => DateTime::createFromFormat('Y', trim($publishedMatch[1], '()')) ?: null,
            ];
        }

        return null;
    }

    /**
     * Match title with subtitles
     *
     * @return array{title: string, subtitle: string}|null
     */
    public function matchBySubtitle(string $title): ?array
    {
        if (str_contains($title, ' - ')) {
            $titleParts = explode(' - ', $title);

            return [
                'title' => array_shift($titleParts),
                'subtitle' => implode(' - ', $titleParts),
            ];
        }

        return null;
    }

    public function getFolderByFilename(string $path): string
    {
        $folder = trim($path);

        $series = $this->matchBySeries($folder);
        if ($series) {
            $folder = $series['title'];
        }

        $volume = $this->matchByVolume($folder);
        if ($volume) {
            $folder = $volume['title'];
        }

        $published = $this->matchByPublished($folder);
        if ($published) {
            $folder = $published['title'];
        }

        $subtitle = $this->matchBySubtitle($folder);
        if ($subtitle) {
            $folder = $subtitle['title'];
        }

        return preg_replace('/[^a-z0-9_-]/i', '', $folder) ?? $folder;
    }
}
