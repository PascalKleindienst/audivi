<?php

declare(strict_types=1);

namespace App\Actions\Library;

use App\Facades\Library;
use Illuminate\Support\Facades\Storage;

class UploadItem
{
    public function uploadChunk(bool $isFirst, string $chunk, string $path): bool
    {
        // TODO: organize in folders
        $parts = explode(';base64,', $chunk);
        if (! \is_array($parts) || ! isset($parts[1])) {
            $decodedChunk = '';
        } else {
            $decodedChunk = base64_decode($parts[1]);
        }

        $result = $isFirst
            ? Storage::disk('library')->put($path, $decodedChunk)
            : Storage::disk('library')->append($path, $decodedChunk, '');

        if (! $result) {
            return false;
        }

        return true;
    }

    public function moveToFolder(string $filename, string $folder, string $fileType): ?string
    {
        if ($fileType === 'application/zip' || $fileType === 'application/x-zip-compressed') {
            $zip = new \ZipArchive();

            if ($zip->open(Storage::disk('library')->path($filename)) !== true) {
                Storage::disk('library')->delete($filename);

                return null;
            }

            $zip->extractTo(Storage::disk('library')->path($folder));
            $zip->close();
            Storage::disk('library')->delete($filename);

            return $folder;
        }

        $folder = Library::getFolderByFilename($folder);
        $path = $folder.'/'.$filename;
        Storage::disk('library')->move($filename, $path);

        return $path;
    }
}
