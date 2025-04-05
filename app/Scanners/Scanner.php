<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Data\Library\ScanResultData;
use App\Events\CollectedScanPathsEvent;
use App\Events\ScanProgressEvent;
use App\Library\LibraryFactory;
use App\Models\AudioBook;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use TypeError;

final class Scanner
{
    /**
     * @var FileScannerInterface[]
     */
    private array $scanners = [];

    public function __construct(private readonly LibraryFactory $library)
    {
    }

    public function addScanner(FileScannerInterface $scanner): void
    {
        $this->scanners[] = $scanner;
    }

    /**
     * @return ItemData[]
     */
    public function getFolderContents(?string $path = null): array
    {
        $paths = collect(Storage::disk('library')->files($path, recursive: true))
            ->filter(static fn (string $file) => ! str_starts_with($file, '.'))
            ->filter(static fn (string $file) => (bool) preg_match('/\.(mp3|wav|ogg|m4a|flac|opus)$/i', $file))
            ->mapToGroups(static fn (string $file) => [
                File::dirname($file) => $file,
            ]);

        CollectedScanPathsEvent::dispatch($paths);

        $items = [];
        foreach ($paths as $dirname => $files) {
            $items[] = $this->library->createLibraryItem($dirname, $files);
        }

        return $items;
    }

    /**
     * Scan Item / folder path, as one item can have many files / tracks
     *
     * @throws InvalidArgumentException if no files are found in path
     */
    public function scanPath(string $path, bool $force = false): void
    {
        $items = $this->getFolderContents($path);

        if (empty($items)) {
            throw new InvalidArgumentException('No files found in path: '.$path);
        }

        foreach ($items as $item) {
            $meta = $this->scanItem($item, $force);

            if ($meta) {
                $this->library->saveLibraryItem([$meta]);
            }
        }
    }

    public function scanItem(ItemData $item, bool $force = false): ?MetaData
    {
        foreach ($this->scanners as $scanner) {
            try {
                $scanner->setItem($item);

                foreach ($item->files as $file) {
                    if (! $force && ! $this->isItemNewOrChanged($item, $file)) {
                        ScanProgressEvent::dispatch(ScanResultData::skipped($file));

                        continue;
                    }

                    ScanProgressEvent::dispatch($scanner->scan($file));
                }

                return $scanner->prepareScanResult();
            } catch (TypeError) {
                continue;
            }
        }

        Log::warning(
            'Could not scan item with any of the registered library scanners',
            ['scanners' => $this->scanners]
        );

        return null;
    }

    public function scanLibrary(bool $force = false): void
    {
        $items = $this->getFolderContents();

        foreach ($items as $item) {
            $meta = $this->scanItem($item, $force);

            if ($meta) {
                $this->library->saveLibraryItem([$meta]);
            }
        }
    }

    public function isItemNewOrChanged(ItemData $item, string $file): bool
    {
        return AudioBook::query()
            ->where('path', $item->meta->path)
            ->whereHas('tracks', static fn ($track) => $track
                ->where('path', basename($file))
                ->where('mTime', '=', Storage::disk('library')->lastModified($file))
            )
            ->first() === null;
    }
}
