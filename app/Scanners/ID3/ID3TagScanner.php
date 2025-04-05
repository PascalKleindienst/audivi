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
use App\Scanners\ParserError;
use App\ValueObjects\Buffer;
use App\ValueObjects\Version;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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

    /** @var TrackData[] */
    private array $tracks = [];

    public function setItem(ItemData $item): void
    {
        // Reset in-memory cache, to avoid leaking data to next items!
        $this->tags = [];
        $this->tracks = [];
        $this->cover = null;

        parent::setItem($item);
    }

    /**
     * @throws TypeError if non of the media files contian ID3 Tags and therefore no metadata could be parsed!
     */
    public function prepareScanResult(): MetaData
    {
        $item = $this->item;

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
        $metadata['duration'] = array_sum(array_map(static fn (TrackData $track) => $track->duration ?? 0, $this->tracks));

        return MetaData::from($metadata + $item->meta->toArray());
    }

    protected function getDuration(string $file): int
    {
        $storage = Storage::disk('library');
        if (! $storage->exists($file) || ! $storage->size($file)) {
            return 0;
        }

        $duration = 0;

        // Read the first 10 bytes to check for ID3v2 tag
        $stream = $storage->readStream($file);
        if ($stream === null) {
            return 0;
        }

        $header = fread($stream, 10);
        if (! $header || \strlen($header) < 10) {
            return 0;
        }

        // Check for ID3v2 tag
        if (str_starts_with($header, 'ID3')) {
            $id3v2Size = (\ord($header[6]) << 21) | (\ord($header[7]) << 14) | (\ord($header[8]) << 7) | \ord($header[9]);
            fseek($stream, $id3v2Size, SEEK_CUR);
        } else {
            fseek($stream, -10, SEEK_CUR);
        }

        $bitrates = [0, 32000, 40000, 48000, 56000, 64000, 80000, 96000, 112000, 128000, 160000, 192000, 224000, 256000, 320000];
        $samplerates = [44100, 48000, 32000];

        // Read through the MP3 file
        while (! feof($stream)) {
            // Read the MP3 frame header
            $frameHeader = fread($stream, 4);
            if (! $frameHeader || \strlen($frameHeader) < 4) {
                break;
            }

            // Check for frame sync (11 bits set)
            if ((\ord($frameHeader[0]) & 0xFF) !== 0xFF || (\ord($frameHeader[1]) & 0xE0) !== 0xE0) {
                break;
            }

            $bitrateIndex = (\ord($frameHeader[2]) & 0xF0) >> 4;
            $sampleRateIndex = (\ord($frameHeader[2]) & 0x0C) >> 2;

            $bitrate = $bitrates[$bitrateIndex] ?? null;
            $samplerate = $samplerates[$sampleRateIndex] ?? null;

            if ($bitrate === null || $samplerate === null) {
                break;
            }

            // Calculate frame length in bytes
            $padding = (\ord($frameHeader[2]) & 0x02) >> 1;
            $frameLength = (144 * $bitrate) / $samplerate + $padding;

            // Calculate duration of the frame
            $frameDuration = (1152 / $samplerate);
            $duration += $frameDuration;

            fseek($stream, (int) $frameLength - 4, SEEK_CUR);
        }

        return (int) floor($duration);
    }

    /**
     * @throws ParserError
     */
    protected function parseFileContent(ItemData $item, string $file, Buffer $content): void
    {
        // Parse ID3 Tag
        $tag = Parser::parseTag($content, Version::from(0, 0));
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
            $this->cover = \dirname($file)."/Cover{$track}.{$extension}";

            Storage::disk('library')->put($this->cover, $coverContent);
        }

        // Add Tag and track
        $this->tags[] = $tag;
        $this->tracks[] = TrackData::from([
            'title' => $tag->title ?? 'UNKNOWN',
            'position' => $tag->track ?? 1,
            'path' => basename($file),
            'duration' => $this->getDuration($file),
            'mTime' => Storage::disk('library')->lastModified($file),
        ]);
    }
}
