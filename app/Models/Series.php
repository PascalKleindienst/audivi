<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SeriesFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AudioBook> $audioBooks
 * @property-read int|null $audio_books_count
 *
 * @method static SeriesFactory factory($count = null, $state = [])
 * @method static Builder|Series newModelQuery()
 * @method static Builder|Series newQuery()
 * @method static Builder|Series query()
 * @method static Builder|Series whereCreatedAt($value)
 * @method static Builder|Series whereId($value)
 * @method static Builder|Series whereName($value)
 * @method static Builder|Series whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Series extends Model
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<AudioBook, $this>
     */
    public function audioBooks(): HasMany
    {
        return $this->hasMany(AudioBook::class);
    }
}
