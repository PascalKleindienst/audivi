<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use App\Data\ID3\ImageType;
use App\Data\ID3\ImageValueData;
use App\Data\ID3\TagData;
use App\Data\ID3\TrackData;
use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Scanners\AudioFileScanner;
use DateTime;
use Illuminate\Support\Arr;
use SplFileInfo;
use TypeError;

/**
 * @see https://id3.org/ID3v1
 * @see https://id3.org/id3v2.3.0
 */
final class ID3TagScanner extends AudioFileScanner
{
    protected ?string $cover = null;

    /** @var TagData[] */
    private array $tags = [];

    /** @var array{title: ?string, position: ?string, path: string}[] */
    private array $tracks = [];

    public function scanItem(ItemData $item): MetaData
    {
        // Reset in-memory cache, to avoid leaking data to next items!
        $this->tags = [];
        $this->tracks = [];
        $this->cover = null;

        return parent::scanItem($item);
    }

    /**
     * @throws TypeError if non of the media files contian ID3 Tags and therefore no metadata could be parsed!
     */
    protected function prepareScanResult(ItemData $item): MetaData
    {
        if (empty($this->tags)) {
            throw new TypeError('Media Files do not contain ID3 Tags!');
        }

        $metadata = [];

        foreach ($this->tags as $tag) {
            $meta = [
                'publisher' => $tag->publisher ?? null,
                'published_at' => $tag->year ? DateTime::createFromFormat('Y', (string) $tag->year) : null,
                'description' => $tag->comments,
                'authors' => $tag->artist,
                'language' => $tag->language,
            ];

            if ($tag->album) {
                $titleParts = explode(' - ', $tag->album);
                $meta['title'] = array_shift($titleParts);
                $meta['subtitle'] = implode(' - ', $titleParts);
            }

            $metadata += array_filter($meta);
        }

        $metadata['cover'] = $this->cover;
        $metadata['tracks'] = $this->tracks;

        return MetaData::from($metadata + $item->meta->toArray());
    }

    protected function parseFileContent(ItemData $item, SplFileInfo $file, string $content): void
    {
        // Parse ID3 Tag
        $tag = Parser::parseTag($content);
        if ($tag === null) {
            return;
        }

        // Get and save cover image if available
        $cover = Arr::first($tag->images, static fn (ImageValueData $image) => $image->type === ImageType::COVER_FRONT);
        if ($cover instanceof ImageValueData) {
            $extension = match ($cover->mime) {
                'image/png' => 'png',
                default => 'jpg',
            };

            $coverContent = $cover->data;
            $track = $tag->track && $tag->track !== '1' ? $tag->track : '';
            $this->cover = $file->getPath()."/Cover{$track}.{$extension}";

            file_put_contents($this->cover, $coverContent);
        }

        // Add Tag and track
        $this->tags[] = $tag;
        $this->tracks[] = TrackData::from([
            'title' => $tag->title ?? 'UNKNOWN',
            'position' => $tag->track,
            'path' => $file->getPath(),
        ]);
    }
}
