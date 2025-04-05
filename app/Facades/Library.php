<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\LibraryService;
use Illuminate\Support\Facades\Facade;

/**
 * @see LibraryService
 */
class Library extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LibraryService::class;
    }
}
