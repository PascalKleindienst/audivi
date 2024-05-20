<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Library\LibraryFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SplFileInfo;
use TypeError;

final class Scanner
{
    /**
     * @var FileScannerInterface[]
     */
    private array $scanners = [];

    public function __construct(private readonly LibraryFactory $library, private readonly LoggerInterface $logger)
    {
    }

    public function addScanner(FileScannerInterface $scanner): void
    {
        $this->scanners[] = $scanner;
    }

    /**
     * @param string $path
     * @return ItemData[]
     */
    public function getFolderContents(string $path): array
    {
        $folder = Storage::disk('library')->fileExists($path) ? File::dirname($path) : $path;
        $folders = Collection::make(Storage::disk('library')->files($folder, true))
            ->map(static fn (string $file) => new SplFileInfo(Storage::disk('library')->path($file)))
            ->groupBy(static fn (SplFileInfo $file) => $file->getPath());

        $items = [];
        foreach ($folders as $dirname => $files) {
            $items[] = $this->library->createLibraryItem($dirname, $files);
        }

        return $items;
    }

    /**
     * Scan Item / folder path, as one item can have many files / tracks
     *
     * @return MetaData[]
     * @throws InvalidArgumentException if no files are found in path
     */
    public function scanPath(string $path): array
    {
        $items = $this->getFolderContents($path);

        if (empty($items)) {
            throw new InvalidArgumentException('No files found in path: '.$path);
        }

        return array_map(fn(ItemData $item) => $this->scanItem($item) ?? $item->meta, $items);
    }

    public function scanItem(ItemData $item): ?MetaData
    {
        foreach ($this->scanners as $scanner) {
            try {
                return $scanner->scanItem($item);
            } catch (TypeError) {
                continue;
            }
        }

        $this->logger->warning(
            'Could not scan item with any of the registered library scanners',
            ['scanners' => $this->scanners]
        );

        return null;
    }

    public function scanLibrary(string $path): void
    {
        throw new RuntimeException("Scan library path {$path}: Not implemented");
    }
}
