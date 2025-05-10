<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use App\Data\PlaylistData;
use App\Models\AudioBook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class GetPlaylistAction
{
    /**
     * @throws ModelNotFoundException<AudioBook> if no track was found
     */
    public function handle(int $id): PlaylistData
    {
        return PlaylistData::from(
            AudioBook::query()
                ->whereHas('tracks', fn (Builder $query) => $query
                    ->where('id', $id)
                )
                ->firstOrFail()
        );
    }
}
