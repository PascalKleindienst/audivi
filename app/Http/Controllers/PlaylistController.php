<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Playlist\GetPlaylistAction;
use App\Http\Requests\UpdatePlaylistRequest;
use Illuminate\Http\JsonResponse;

use function response;

final class PlaylistController extends Controller
{
    public function update(UpdatePlaylistRequest $request, GetPlaylistAction $action): JsonResponse
    {
        return response()->json($action->handle($request->get('trackId')));
    }
}
