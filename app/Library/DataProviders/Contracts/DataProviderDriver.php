<?php

declare(strict_types=1);

namespace App\Library\DataProviders\Contracts;

use App\Data\AuthorData;
use App\Library\DataProviders\DataType;
use App\Library\DataProviders\UnsupportedDataTypeError;
use Illuminate\Support\Collection;

interface DataProviderDriver
{
    public static function identifier(): string;

    public function supports(DataType $type): bool;

    /**
     * @return Collection<int, AuthorData>
     *
     * @throws UnsupportedDataTypeError if the provider does not support the data type
     */
    public function search(string $query, DataType $type, ?string $locale = null): Collection;

    /**
     * @throws UnsupportedDataTypeError if the provider does not support the data type
     */
    public function fetch(string|int $id, DataType $type, ?string $locale = null): AuthorData;
}
