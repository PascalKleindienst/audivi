<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Concerns;

use App\Enums\DataProviderType;
use App\Library\DataProviders\Contracts\AuthorDataProvider;
use App\Library\DataProviders\Contracts\BookDataProvider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

trait IsDataProvider
{
    private ?PendingRequest $client = null;

    public function supports(DataProviderType $type): bool
    {
        return match ($type) {
            DataProviderType::AUTHOR => $this instanceof AuthorDataProvider,
            DataProviderType::BOOK => $this instanceof BookDataProvider,
            default => false
        };
    }

    /**
     * @param  array<string, string>  $items
     * @param  callable(PendingRequest, string): Response  $callback
     * @return Response[]
     */
    private function fetchMultipleItems(array $items, callable $callback, int $limit = 10): array
    {
        $items = array_unique($items);
        $items = array_splice($items, 0, $limit);

        return $this->getClient()->pool(fn (Pool $pool) => Arr::map(
            $items,
            fn (string $item, string $id) => $callback($pool->withOptions($this->getClientOptions()), $id)
        ));
    }

    /**
     * @param  Response[]  $responses
     * @return Collection<int|string, array<mixed>>
     */
    private function getResponses(array $responses): Collection
    {
        return collect($responses)
            ->filter(fn ($response) => $response->ok())
            ->map(fn (Response $response) => $response->json());
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
