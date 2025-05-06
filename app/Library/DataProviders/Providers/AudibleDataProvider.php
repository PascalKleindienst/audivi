<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Providers;

use App\Data\AuthorData;
use App\Library\DataProviders\Concerns\IsAuthorDataProvider;
use App\Library\DataProviders\Concerns\IsDataProvider;
use App\Library\DataProviders\Contracts\AuthorDataProvider;
use App\Library\DataProviders\Contracts\DataProviderDriver;
use App\Library\DataProviders\DataType;
use App\Library\DataProviders\UnsupportedDataTypeError;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @phpstan-type AuthorApiResponse array{asin: string, name: string, image: string|null, description: string|null}
 *
 * @implements AuthorDataProvider<AuthorApiResponse>
 */
final class AudibleDataProvider implements AuthorDataProvider, DataProviderDriver
{
    use IsAuthorDataProvider;
    use IsDataProvider;

    public static function identifier(): string
    {
        return 'audible';
    }

    public function search(string $query, DataType $type, ?string $locale = null): Collection
    {
        if (! $this->supports($type)) {
            throw UnsupportedDataTypeError::from($type);
        }

        $cacheKey = 'audible:search:'.$query.':'.$locale;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        return $this->searchAuthor($query, $locale);
    }

    /**
     * @return Collection<int, AuthorData>
     */
    public function searchAuthor(string $query, ?string $locale = null): Collection
    {
        $cacheKey = 'audible:search:'.$query.':'.$locale;

        $response = $this->getClient()->withQueryParameters(['name' => $query, 'region' => $locale ?? app()->getLocale()])->get('authors');
        $authors = array_column($response->json(), 'name', 'asin');

        $responses = $this->fetchMultipleAuthorDetails($authors, static fn (PendingRequest $request, string $id) => $request
            ->withUrlParameters(['asin' => $id])
            ->withQueryParameters(['region' => $locale])
            ->get('authors/{asin}')
        );

        return Cache::remember($cacheKey, now()->addMinutes(60), fn () => $this
            ->mapAuthorResponses($responses)
            ->filter(function (array $response) {
                /** @var AuthorApiResponse $response */
                return ! empty($response['description']) || ! empty($response['image']);
            })
            ->map(function (array $response) {
                /** @var AuthorApiResponse $response */
                return $this->mapAuthorDetails($response);
            })
        );
    }

    public function fetch(int|string $id, DataType $type, ?string $locale = null): AuthorData
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
