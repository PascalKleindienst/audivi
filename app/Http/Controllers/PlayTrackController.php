<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Playlist\StreamTrackAction;
use App\Models\Track;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function response;

final class PlayTrackController extends Controller
{
    public function __invoke(Track $track, Request $request, StreamTrackAction $action): StreamedResponse
    {
        ['status' => $status, 'headers' => $headers, 'callback' => $callback] = $action->handle($track, $request->header('Range'));

        return response()->stream($callback, $status, $headers);
    }
}
