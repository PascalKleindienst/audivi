<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Concerns;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait IsAuthorDataProvider
{
    /**
     * @param  array<string, string>  $authors
     * @param  callable(PendingRequest, string): Response  $callback
     * @return Response[]
     */
    private function fetchMultipleAuthorDetails(array $authors, callable $callback): array
    {
        $authors = array_splice($authors, 0, 10);

        return $this->getClient()->pool(fn (Pool $pool) => Arr::map(
            $authors,
            fn (string $author, string $id) => $callback($pool->withOptions($this->getClientOptions()), $id)
        ));
    }

    /**
     * @param  Response[]  $responses
     * @return Collection<int|string, array<mixed>>
     */
    private function mapAuthorResponses(array $responses): Collection
    {
        return collect($responses)
            ->filter(fn ($response) => $response->ok())
            ->map(fn (Response $response) => $response->json());
    }
}
