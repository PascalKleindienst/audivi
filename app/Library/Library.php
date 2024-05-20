<?php

declare(strict_types=1);

namespace App\Library;

use Illuminate\Support\Facades\Facade;

/**
 * @see LibraryService
 */
class Library extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'library.service';
    }
}
