<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Providers;

use App\Data\AuthorData;
use App\Library\DataProviders\Concerns\IsDataProvider;
use App\Library\DataProviders\Contracts\AuthorDataProvider;
use App\Library\DataProviders\Contracts\DataProviderDriver;
use App\Library\DataProviders\DataType;
use App\Library\DataProviders\UnsupportedDataTypeError;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @phpstan-type AuthorApiResponse array{
 *     key: string,
 *     name: string,
 *     bio: string|null,
 *     links: array<int,
 *     array{url: string}>|null,
 *     remote_ids: array<string, string>|null
 * }
 *
 * @implements AuthorDataProvider<AuthorApiResponse>
 */
final class OpenLibraryDataProvider implements AuthorDataProvider, DataProviderDriver
{
    use IsDataProvider;

    public static function identifier(): string
    {
        return 'openlibrary';
    }

    public function search(string $query, DataType $type, ?string $locale = null): Collection
    {
        if (! $this->supports($type)) {
            throw UnsupportedDataTypeError::from($type);
        }

        return Cache::remember('openlibrary:search:'.$query, now()->addMinutes(60), fn () => $this->searchAuthor($query, $locale));
    }

    public function searchAuthor(string $query, ?string $locale = null): Collection
    {
        // Search for all authors and then get the details
        $response = $this->getClient()->withQueryParameters(['q' => $query])->get('search/authors.json');
        $authors = array_column($response->json()['docs'] ?? [], 'name', 'key');

        $responses = $this->fetchMultipleItems($authors, static fn (PendingRequest $request, string $id) => $request
            ->withUrlParameters(['olid' => $id])
            ->get('authors/{olid}.json')
        );

        return $this
            ->getResponses($responses)
            ->map(function (array $response) {
                /** @var AuthorApiResponse $response */
                return $this->mapAuthorDetails($response);
            });
    }

    public function fetch(int|string $id, DataType $type, ?string $locale = null): AuthorData
    {
        $cacheKey = 'openlibrary:fetch:'.$id.':'.$locale;

        if (! $this->supports($type)) {
            throw UnsupportedDataTypeError::from($type);
        }

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->getClient()->withUrlParameters(['olid' => $id])
            ->get('authors/{olid}.json')
            ->json();

        return Cache::remember($cacheKey, now()->addMinutes(60), fn () => $this->mapAuthorDetails($response));
    }

    /**
     * @param  AuthorApiResponse  $response
     */
    public function mapAuthorDetails(mixed $response): AuthorData
    {
        return AuthorData::from([
            // 'id' => str_replace('/authors/', '', $response['key']),
            'name' => $response['name'],
            'link' => Arr::first($response['links'] ?? [], default: [])['url'] ?? null,
            'image' => 'https://covers.openlibrary.org/a/olid/'.(str_replace('/authors/', '', $response['key'])).'-M.jpg',
            'description' => $response['bio'] ?? null,
            'identifiers' => ['olid' => str_replace('/authors/', '', $response['key']), ...$response['remote_ids'] ?? []],
        ]);
    }
}
