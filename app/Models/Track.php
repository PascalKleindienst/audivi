<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property int $position
 * @property string|null $path
 * @property float|null $start
 * @property float|null $end
 * @property int $audio_book_id
 * @property-read \App\Models\AudioBook $audioBook
 * @method static \Database\Factories\TrackFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Track newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Track newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Track query()
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereAudioBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Track whereTitle($value)
 * @mixin \Eloquent
 */
class Track extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'position',
        'path',
        'start',
        'end',
    ];

    public function audioBook(): BelongsTo
    {
        return $this->belongsTo(AudioBook::class);
    }
}
