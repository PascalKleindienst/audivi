<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Data\Library\ScanResultData;
use App\Exceptions\FileError;
use App\ValueObjects\Buffer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

abstract class AudioFileScanner implements FileScannerInterface
{
    protected const MAX_FILE_CONTENT = 16 * 1024 * 1024; // 16 MiB

    protected ItemData $item;

    abstract protected function parseFileContent(ItemData $item, string $file, Buffer $content): void;

    public function setItem(ItemData $item): void
    {
        $this->item = $item;
    }

    public function scan(string $file): ScanResultData
    {
        Log::debug('Scanning file', ['file' => $file, 'scanner' => static::class]);
        $storage = Storage::disk('library');

        if (! $storage->exists($file) || $storage->size($file) === 0) {
            Log::debug(
                'Skipping file because it is not readable or has no content',
                ['file' => $file, 'scanner' => static::class]
            );

            return ScanResultData::error($file, 'File is not readable or empty');
        }

        try {
            $content = $this->getFileContent($file);
        } catch (Throwable $th) {
            Log::debug('Could read file content', ['file' => $file, 'scanner' => static::class]);

            return ScanResultData::error($file, $th->getMessage());
        }

        $this->parseFileContent($this->item, $file, Buffer::from($content));

        return ScanResultData::success($file);
    }

    /**
     * Prepare the metadata based on the scanned media files for the item.
     */
    public function prepareScanResult(): MetaData
    {
        return $this->item->meta;
    }

    /**
     * @throws FileError if the file could not be read
     */
    protected function getFileContent(string $file): string
    {
        $storage = Storage::disk('library');
        $resource = $storage->readStream($file);

        if ($resource === null) {
            throw new FileError('Could not open file');
        }

        $content = stream_get_contents($resource, min($storage->size($file), self::MAX_FILE_CONTENT));
        fclose($resource);

        if ($content === false) {
            throw new FileError('Could read file content');
        }

        return $content;
    }
}
