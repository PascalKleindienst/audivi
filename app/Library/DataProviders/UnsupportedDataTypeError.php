<?php

declare(strict_types=1);

namespace App\Library\DataProviders;

use Exception;

use function sprintf;

class UnsupportedDataTypeError extends Exception
{
    public static function from(DataType $type): self
    {
        return new self(sprintf('Data type "%s" is not supported', $type->name));
    }
}
