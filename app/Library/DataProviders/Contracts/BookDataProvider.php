<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Contracts;

use Illuminate\Support\Collection;

interface BookDataProvider
{
    /**
     * @return Collection<int|string, array<string, mixed>>
     */
    public function searchBook(string $query, ?string $locale = null): Collection;
}
