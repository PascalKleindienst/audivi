<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'link',
    ];

    public function audioBooks(): BelongsToMany
    {
        return $this->belongsToMany(AudioBook::class);
    }
}
