<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Carbon|null $published_at
 * @property int $id
 * @property string $path
 * @property string $title
 * @property string|null $subtitle
 * @property float|null $volume
 * @property string|null $description
 * @property float|null $rating
 * @property string|null $cover
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Author> $authors
 * @property-read int|null $authors_count
 *
 * @method static \Database\Factories\AudioBookFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook query()
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereSubtitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AudioBook whereVolume($value)
 *
 * @mixin \Eloquent
 */
class AudioBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'title',
        'subtitle',
        'volume',
        'description',
        'rating',
        'cover',
        'published_at',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
