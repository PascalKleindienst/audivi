<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TrackFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property int $position
 * @property string|null $path
 * @property float|null $start
 * @property float|null $end
 * @property int $audio_book_id
 * @property int|null $duration
 * @property int|null $mTime
 * @property-read AudioBook $audioBook
 *
 * @method static TrackFactory factory($count = null, $state = [])
 * @method static Builder|Track newModelQuery()
 * @method static Builder|Track newQuery()
 * @method static Builder|Track query()
 * @method static Builder|Track whereAudioBookId($value)
 * @method static Builder|Track whereEnd($value)
 * @method static Builder|Track whereId($value)
 * @method static Builder|Track wherePath($value)
 * @method static Builder|Track wherePosition($value)
 * @method static Builder|Track whereStart($value)
 * @method static Builder|Track whereTitle($value)
 * @method static Builder<static>|Track whereDuration($value)
 * @method static Builder<static>|Track whereMTime($value)
 *
 * @mixin Eloquent
 */
final class Track extends Model
{
    /** @use HasFactory<TrackFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'position',
        'path',
        'start',
        'end',
        'duration',
        'mTime',
        'audio_book_id',
    ];

    /**
     * @return BelongsTo<AudioBook, $this>
     */
    public function audioBook(): BelongsTo
    {
        return $this->belongsTo(AudioBook::class);
    }
}
