<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PublisherFactory;
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
 * @method static PublisherFactory factory($count = null, $state = [])
 * @method static Builder|Publisher newModelQuery()
 * @method static Builder|Publisher newQuery()
 * @method static Builder|Publisher query()
 * @method static Builder|Publisher whereCreatedAt($value)
 * @method static Builder|Publisher whereId($value)
 * @method static Builder|Publisher whereName($value)
 * @method static Builder|Publisher whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Publisher extends Model
{
    /** @use HasFactory<PublisherFactory> */
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
