<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Concerns;

use App\Library\DataProviders\Contracts\AuthorDataProvider;
use App\Library\DataProviders\DataType;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait IsDataProvider
{
    private ?PendingRequest $client = null;

    public function supports(DataType $type): bool
    {
        return match ($type) {
            DataType::AUTHOR => $this instanceof AuthorDataProvider,
            default => false
        };
    }

    private function getClient(): PendingRequest
    {
        return $this->client ??= Http::withOptions($this->getClientOptions());
    }

    /**
     * @return array{base_uri: string, timeout: int, connect_timeout: int}
     */
    private function getClientOptions(): array
    {
        $config = config('audivi.data_providers', [])[self::identifier()] ?? [];

        return [
            'base_uri' => $config['base_uri'] ?? '',
            'timeout' => $config['timeout'] ?? 10,
            'connect_timeout' => $config['connect_timeout'] ?? 2,
        ];
    }
}
