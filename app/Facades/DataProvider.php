<?php

declare(strict_types=1);

namespace App\Facades;

use App\Library\DataProviders\DataProviderManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see DataProviderManager
 */
final class DataProvider extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DataProviderManager::class;
    }
}
