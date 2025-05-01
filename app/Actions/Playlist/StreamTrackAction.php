<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use App\Models\Track;
use Closure;
use Illuminate\Support\Facades\Storage;

final class StreamTrackAction
{
    /**
     * @return array{status: 200|206, headers: array<string, mixed>, callback: Closure(): void}
     */
    public function handle(Track $track, ?string $rangeHeader = null): array
    {
        $path = $track->audioBook->path.'/'.$track->path;
        if (! Storage::disk('library')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('library')->path($path);
        $size = Storage::disk('library')->size($path);
        $mimeType = Storage::disk('library')->mimeType($path);

        $start = 0;
        $end = $size - 1;
        $status = 200;

        // Handle Range header
        if ($rangeHeader) {
            // Status becomes 206 (Partial Content)
            $status = 206;

            // Parse Range header value
            [, $range] = explode('=', $rangeHeader, 2);

            // We don't support multiple ranges
            if (str_contains($range, ',')) {
                abort(416, 'Requested Range Not Satisfiable');
            }

            // Range was: -X (last X bytes)
            if ($range === '-') {
                $start = $size - (float) substr($range, 1);
            } else {
                // Range was: X-Y or X-
                $range = explode('-', $range);
                $start = $range[0];
                $end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size - 1;
            }

            // Validate ranges
            $start = max(0, (int) $start);
            $end = min($size - 1, (int) $end);

            if ($start > $end || $start >= $size || $end >= $size) {
                abort(416, 'Requested Range Not Satisfiable');
            }
        }

        $length = $end - $start + 1;

        // Set headers
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ];

        // Add Content-Range header for range requests
        if ($status === 206) {
            $headers['Content-Range'] = "bytes $start-$end/$size";
        }

        return [
            'status' => $status,
            'headers' => $headers,
            'callback' => fn () => $this->stream($file, $start, $end),
        ];
    }

    private function stream(string $path, int $start, int $end): void
    {
        $handle = fopen($path, 'rb');
        abort_if($handle === false, 404);

        fseek($handle, $start);

        $buffer = 1024 * 8;
        $currentPosition = $start;

        while (! feof($handle) && $currentPosition <= $end) {
            $readLength = min($buffer, $end - $currentPosition + 1);
            echo fread($handle, $readLength); // @phpstan-ignore-line
            flush();
            $currentPosition += $readLength;
        }

        fclose($handle);
    }
}
