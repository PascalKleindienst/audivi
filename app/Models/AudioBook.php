<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\AudioBookFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property-read Collection<int, Author> $authors
 * @property-read int|null $authors_count
 *
 * @method static AudioBookFactory factory($count = null, $state = [])
 * @method static Builder|AudioBook newModelQuery()
 * @method static Builder|AudioBook newQuery()
 * @method static Builder|AudioBook query()
 * @method static Builder|AudioBook whereCover($value)
 * @method static Builder|AudioBook whereCreatedAt($value)
 * @method static Builder|AudioBook whereDescription($value)
 * @method static Builder|AudioBook whereId($value)
 * @method static Builder|AudioBook wherePath($value)
 * @method static Builder|AudioBook wherePublishedAt($value)
 * @method static Builder|AudioBook whereRating($value)
 * @method static Builder|AudioBook whereSubtitle($value)
 * @method static Builder|AudioBook whereTitle($value)
 * @method static Builder|AudioBook whereUpdatedAt($value)
 * @method static Builder|AudioBook whereVolume($value)
 *
 * @property string|null $language
 * @property int|null $publisher_id
 * @property int|null $series_id
 * @property-read Publisher|null $publisher
 * @property-read Series|null $series
 * @property-read Collection<int, Track> $tracks
 * @property-read int|null $tracks_count
 *
 * @method static Builder|AudioBook whereLanguage($value)
 * @method static Builder|AudioBook wherePublisherId($value)
 * @method static Builder|AudioBook whereSeriesId($value)
 *
 * @property int|null $duration
 *
 * @method static Builder<static>|AudioBook whereDuration($value)
 *
 * @mixin Eloquent
 */
final class AudioBook extends Model
{
    /** @use HasFactory<AudioBookFactory> */
    use HasFactory;

    protected $fillable = [
        'path',
        'title',
        'subtitle',
        'volume',
        'description',
        'rating',
        'cover',
        'language',
        'duration',
        'published_at',
    ];

    /**
     * @return BelongsToMany<Author, $this>
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    /**
     * @return BelongsTo<Publisher, $this>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * @return BelongsTo<Series, $this>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * @return HasMany<Track, $this>
     */
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('position');
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        $format = 'datetime:'.config('data.date_format');

        return [
            'published_at' => $format,
            'created_at' => $format,
            'updated_at' => $format,
        ];
    }
}
