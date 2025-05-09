<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Contracts;

use App\Data\AuthorData;
use Illuminate\Support\Collection;

/**
 * @template TResponse of array
 */
interface AuthorDataProvider
{
    /**
     * @param  TResponse  $response
     * @return AuthorData
     *                    //array{id: string, name: string, link: string|null, cover: string|null, description: string|null, identifiers:
     *                    array<string, string>}
     */
    public function mapAuthorDetails(array $response): AuthorData;

    /**
     * @return Collection<int|string, AuthorData>
     */
    public function searchAuthor(string $query, ?string $locale = null): Collection;
}
