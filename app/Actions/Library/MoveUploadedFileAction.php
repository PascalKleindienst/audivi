<?php

declare(strict_types=1);

namespace App\Actions\Library;

use App\Facades\Library;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

final readonly class MoveUploadedFileAction
{
    public function handle(string $filename, string $folder, string $fileType): ?string
    {
        if ($fileType === 'application/zip' || $fileType === 'application/x-zip-compressed') {
            $zip = new ZipArchive();

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
