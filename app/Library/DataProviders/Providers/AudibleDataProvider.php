<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Providers;

use App\Data\AuthorData;
use App\Enums\DataProviderType;
use App\Exceptions\UnsupportedDataTypeError;
use App\Library\DataProviders\Concerns\IsDataProvider;
use App\Library\DataProviders\Contracts\AuthorDataProvider;
use App\Library\DataProviders\Contracts\BookDataProvider;
use App\Library\DataProviders\Contracts\DataProviderDriver;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * @phpstan-type AuthorApiResponse array{asin: string, name: string, image: string|null, description: string|null}
 *
 * @implements AuthorDataProvider<AuthorApiResponse>
 */
final class AudibleDataProvider implements AuthorDataProvider, BookDataProvider, DataProviderDriver
{
    use IsDataProvider;

    public static function identifier(): string
    {
        return 'audible';
    }

    public function search(string $query, DataProviderType $type, ?string $locale = null): Collection
    {
        if (! $this->supports($type)) {
            throw UnsupportedDataTypeError::from($type);
        }

        // normalize query
        $query = preg_replace('/\(.+?\)/', '', $query) ?? $query;

        $cacheKey = 'audible:search:'.$query.':'.$locale;

        if ($type === DataProviderType::BOOK) {
            return Cache::remember($cacheKey, now()->addMinutes(60), fn () => $this->searchBook($query, $locale));
        }

        return Cache::remember($cacheKey, now()->addMinutes(60), fn () => $this->searchAuthor($query, $locale));
    }

    public function searchBook(string $query, ?string $locale = null): Collection
    {
        $response = Http::withOptions([
            'base_uri' => 'https://api.audible.de/1.0/catalog/',
            'timeout' => config('audivi.data_providers.audible.timeout', 10),
            'connect_timeout' => config('audivi.data_providers.audible.connect_timeout', 2),
        ])
            ->withQueryParameters(['title' => $query])->get('products');

        if (! $response->ok()) {
            return collect();
        }

        $ids = array_column($response->json('products', []), 'asin', 'asin');
        $responses = $this->fetchMultipleItems($ids, static fn (PendingRequest $request, string $id) => $request
            ->withUrlParameters(['asin' => $id])
            ->withQueryParameters(['region' => $locale])
            ->get('books/{asin}'));

        return $this->getResponses($responses)->map(fn ($response) => [
            'title' => $response['title'],
            'subtitle' => $response['subtitle'] ?? null,
            'cover' => $response['image'] ?? null,
            'language' => $response['language'] ?? $locale,
            'description' => strip_tags($response['summary'] ?? ''),
            'publisher' => $response['publisherName'] ?? null,
            'releaseDate' => $response['releaseDate'] ?? null,
            'volume' => $response['seriesPrimary']['position'] ?? null,
            'series' => $response['seriesPrimary']['name'] ?? null,
            'copyright' => $response['copyright'] ?? null,
            'authors' => Arr::map($response['authors'] ?? [], fn ($author) => $author['name']),
            'narrators' => Arr::map($response['narrators'] ?? [], fn ($author) => $author['name']),
            'rating' => $response['rating'] ?? null,
            'tags' => Arr::map($response['genres'] ?? [], fn ($genre) => $genre['name']),
            'identifiers' => ['asin' => $response['asin']],
        ]);
    }

    public function searchAuthor(string $query, ?string $locale = null): Collection
    {
        $response = $this->getClient()->withQueryParameters(['name' => $query, 'region' => $locale ?? app()->getLocale()])->get('authors');
        $authors = array_column($response->json(), 'name', 'asin');

        $responses = $this->fetchMultipleItems($authors, static fn (PendingRequest $request, string $id) => $request
            ->withUrlParameters(['asin' => $id])
            ->withQueryParameters(['region' => $locale])
            ->get('authors/{asin}')
        );

        return $this
            ->getResponses($responses)
            ->filter(function (array $response) {
                /** @var AuthorApiResponse $response */
                return ! empty($response['description']) || ! empty($response['image']);
            })
            ->map(function (array $response) {
                /** @var AuthorApiResponse $response */
                return $this->mapAuthorDetails($response);
            });
    }

    public function fetch(int|string $id, DataProviderType $type, ?string $locale = null): AuthorData
    {
        $cacheKey = 'audivi:fetch:'.$id.':'.$locale;

        if (! $this->supports($type)) {
            throw UnsupportedDataTypeError::from($type);
        }

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->getClient()->withUrlParameters(['asin' => $id])->withQueryParameters(['region' => $locale])
            ->get('authors/{asin}')
            ->json();

        // if ($response['statusCode'] ?? false && $response['statusCode'] === 500) {
        //     return [];
        // }

        return Cache::remember($cacheKey, now()->addMinutes(60), fn () => $this->mapAuthorDetails($response));
    }

    /**
     * @param  AuthorApiResponse  $response
     */
    public function mapAuthorDetails(array $response): AuthorData
    {
        $image = $response['image'] ?? null;
        if ($image) {
            $image = str_replace(['_SX120_', '120,120_'], ['_SX240_', '240,240_'], $image); // change image size
        }

        return AuthorData::from([
            'name' => $response['name'],
            'image' => $image,
            'description' => $response['description'] ?? null,
            'identifiers' => ['asin' => $response['asin']],
        ]);
    }
}
