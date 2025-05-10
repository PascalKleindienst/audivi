<?php

declare(strict_types=1);

namespace App\Actions\Library;

use Illuminate\Support\Facades\Storage;

use function is_array;

final readonly class UploadItemChunkAction
{
    public function handle(bool $isFirst, string $chunk, string $path): bool
    {
        // TODO: organize in folders
        $parts = explode(';base64,', $chunk);
        if (! is_array($parts) || ! isset($parts[1])) {
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
}
