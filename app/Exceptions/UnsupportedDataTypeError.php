<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\DataProviderType;
use Exception;

use function sprintf;

final class UnsupportedDataTypeError extends Exception
{
    public static function from(DataProviderType $type): self
    {
        return new self(sprintf('Data type "%s" is not supported', $type->name));
    }
}
