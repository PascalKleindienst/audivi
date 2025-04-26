<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AuthorFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string|null $description
 * @property string|null $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AudioBook> $audioBooks
 * @property-read int|null $audio_books_count
 *
 * @method static AuthorFactory factory($count = null, $state = [])
 * @method static Builder|Author newModelQuery()
 * @method static Builder|Author newQuery()
 * @method static Builder|Author query()
 * @method static Builder|Author whereCreatedAt($value)
 * @method static Builder|Author whereDescription($value)
 * @method static Builder|Author whereId($value)
 * @method static Builder|Author whereImage($value)
 * @method static Builder|Author whereLink($value)
 * @method static Builder|Author whereName($value)
 * @method static Builder|Author whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'link',
    ];

    /**
     * @return BelongsToMany<AudioBook, $this>
     */
    public function audioBooks(): BelongsToMany
    {
        return $this->belongsToMany(AudioBook::class);
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:'.config('data.date_format'),
            'updated_at' => 'datetime:'.config('data.date_format'),
        ];
    }
}
