<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Playlist\GetPlaylistAction;
use App\Actions\Playlist\StreamTrackAction;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Models\Track;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function response;

final class PlaylistController extends Controller
{
    public function update(UpdatePlaylistRequest $request, GetPlaylistAction $action): JsonResponse
    {
        return response()->json($action->handle($request->get('trackId')));
    }

    public function play(Track $track, Request $request, StreamTrackAction $action): StreamedResponse
    {
        ['status' => $status, 'headers' => $headers, 'callback' => $callback] = $action->handle($track, $request->header('Range'));

        return response()->stream($callback, $status, $headers);
    }
}
