<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\ValueObjects\Buffer;
use Psr\Log\LoggerInterface;

abstract class AudioFileScanner implements FileScannerInterface
{
    protected const MAX_FILE_CONTENT = 16 * 1024 * 1024; // 16 MiB

    public function __construct(protected LoggerInterface $log)
    {
    }

    public function scanItem(ItemData $item): MetaData
    {
        foreach ($item->mediaFiles as $file) {
            $this->log->debug('Scanning file', ['file' => $file, 'scanner' => static::class]);

            if (! $file->isFile() || empty($file->getSize()) || ! $file->isReadable()) {
                $this->log->debug(
                    'Skipping file because it is not readable or has no content',
                    ['file' => $file, 'scanner' => static::class]
                );

                continue;
            }

            // TODO: Use File::
            $resource = fopen($file->getPathname(), 'rb');

            if (empty($resource)) {
                $this->log->debug('Could not open file', ['file' => $file, 'scanner' => static::class]);

                continue;
            }

            $content = stream_get_contents($resource, min($file->getSize(), self::MAX_FILE_CONTENT));
            fclose($resource);

            if ($content === false) {
                $this->log->debug('Could read file content', ['file' => $file, 'scanner' => static::class]);

                continue;
            }

            $this->parseFileContent($item, $file, Buffer::from($content));
        }

        return $this->prepareScanResult($item);
    }

    /**
     * Prepare the metadata based on the scanned media files for the item.
     */
    protected function prepareScanResult(ItemData $item): MetaData
    {
        return $item->meta;
    }

    abstract protected function parseFileContent(ItemData $item, \SplFileInfo $file, Buffer $content): void;
}
