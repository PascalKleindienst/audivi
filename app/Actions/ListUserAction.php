<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UserData;
use App\Models\User;
use Spatie\LaravelData\PaginatedDataCollection;

final class ListUserAction
{
    /**
     * @param  array{search?: string, sort?: string, direction?: string}  $filter
     * @return PaginatedDataCollection<string|int, UserData>
     */
    public function handle(array $filter): PaginatedDataCollection
    {
        return UserData::collect(
            User::query()
                ->when($filter['search'] ?? false, static fn ($query, string|false $search) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                )
                ->when($filter['sort'] ?? false, static fn ($query, string|false $sort) => $query
                    ->orderBy((string) $sort, (string) ($filter['direction'] ?? 'asc'))
                )
                ->paginate()->withQueryString(),
            PaginatedDataCollection::class
        );
    }
}
