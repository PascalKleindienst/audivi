<?php

declare(strict_types=1);

namespace App\Library\DataProviders;

use App\Library\DataProviders\Contracts\DataProviderDriver;
use App\Library\DataProviders\Providers\AudibleDataProvider;
use App\Library\DataProviders\Providers\OpenLibraryDataProvider;
use Illuminate\Support\Manager;

/**
 * @mixin DataProviderDriver
 *
 * @method DataProviderDriver driver($driver = null)
 */
final class DataProviderManager extends Manager
{
    public function provider(?string $provider = null): DataProviderDriver
    {
        return $this->driver($provider);
    }

    /**
     * @return list<int|string>
     */
    public function providers(): array
    {
        return array_keys(config('audivi.data_providers', []));
    }

    public function createAudibleDriver(): AudibleDataProvider
    {
        return app(AudibleDataProvider::class);
    }

    public function createOpenLibraryDriver(): OpenLibraryDataProvider
    {
        return app(OpenLibraryDataProvider::class);
    }

    public function getDefaultDriver(): string
    {
        return AudibleDataProvider::identifier();
    }
}
