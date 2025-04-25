<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Data\TrackData;
use App\ValueObjects\Buffer;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use JsonException;
use TypeError;

use function dirname;

final class FFMpegScanner extends AudioFileScanner
{
    protected ?string $cover = null;

    /**
     * @var array<string, string>
     */
    private array $metadata = [];

    /** @var TrackData[] */
    private array $tracks = [];

    public function setItem(ItemData $item): void
    {
        // Reset in-memory cache, to avoid leaking data to next items!
        $this->metadata = [];
        $this->tracks = [];
        $this->cover = null;

        parent::setItem($item);
    }

    public function prepareScanResult(): MetaData
    {
        if (empty($this->metadata)) {
            throw new TypeError('Could not parse media files with ffmpeg!');
        }

        $metadata = [];
        foreach ($this->metadata as $meta) {
            $metadata += array_filter($meta);
        }

        $metadata['cover'] = $this->cover;
        $metadata['tracks'] = $this->tracks;

        return MetaData::from($metadata + $this->item->meta->toArray());
    }

    /**
     * @return string[][]
     *
     * @throws JsonException if we cannot parse the ffmpeg output
     */
    private function getFileMetadata(string $file): array
    {
        $process = Process::run([
            'ffprobe',
            '-i',
            $file,
            '-show_entries',
            'format',
            '-of',
            'json',
        ]);

        if ($process->output() === '') {
            throw new TypeError('Could not parse file with FFMpeg: '.$file);
        }

        return json_decode($process->output(), true, flags: JSON_THROW_ON_ERROR)['format'] ?? [];
    }

    /**
     * @return string[][]
     *
     * @throws JsonException if we cannot parse the ffmpeg output
     */
    private function getChapters(string $file): array
    {
        $process = Process::run([
            'ffprobe',
            '-i',
            $file,
            '-show_chapters',
            '-of',
            'json',
        ]);

        if ($process->output() === '') {
            throw new TypeError('Could not parse chapters with FFMpeg: '.$file);
        }

        return json_decode($process->output(), true, flags: JSON_THROW_ON_ERROR)['chapters'] ?? [];
    }

    private function getCover(string $file): string
    {
        $process = Process::run([
            'ffmpeg',
            '-i',
            $file,
            '-an',
            '-vcodec',
            'copy',
            '-f',
            'image2pipe',
            'pipe:1',
        ]);

        return $process->output();
    }

    /**
     * @throws JsonException if we cannot parse the ffmpeg output
     */
    protected function parseFileContent(ItemData $item, string $file, Buffer $content): void
    {
        $path = Storage::disk('library')->path($file);
        $fileMetaData = $this->getFileMetadata($path);

        $metadata = [];
        $metadata['title'] = $fileMetaData['tags']['title'];
        $metadata['volume'] = $fileMetaData['tags']['track'] ?? null;
        $metadata['description'] = $fileMetaData['tags']['comment'] ?? null;
        $metadata['publisher'] = $fileMetaData['tags']['publisher'] ?? null;
        $metadata['language'] = $fileMetaData['tags']['language'] ?? null;
        $metadata['authors'] = $fileMetaData['tags']['artist'];

        if ($fileMetaData['tags']['album']) {
            $titleParts = preg_split('/[:|-] /', $fileMetaData['tags']['album']);
            $metadata['title'] = array_shift($titleParts);
            $metadata['subtitle'] = implode(' - ', $titleParts);
        }

        $metadata['duration'] = $fileMetaData['duration'];

        $this->metadata[] = $metadata;

        // Tracks / Chapters
        $chapters = $this->getChapters($path);

        foreach ($chapters as $chapter) {
            $this->tracks[] = TrackData::from([
                'title' => $chapter['tags']['title'] ?? '',
                'position' => $chapter['id'],
                'path' => basename($file),
                'start' => (float) $chapter['start_time'],
                'end' => (float) $chapter['end_time'],
                'duration' => (float) $chapter['end_time'] - (float) $chapter['start_time'],
            ]);
        }

        // image
        $cover = $this->getCover($path);
        if (! empty($cover)) {
            $this->cover = dirname($file).'/Cover.jpg';
            Storage::disk('library')->put($this->cover, $cover);
            Storage::disk('public')->put($this->cover, $cover);
        }
    }
}
