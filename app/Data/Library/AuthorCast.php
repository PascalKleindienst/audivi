<?php

declare(strict_types=1);

namespace App\Data\Library;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class AuthorCast implements Cast
{
    /**
     * @phpstan-ignore-next-line
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (\is_string($value)) {
            $authors = preg_split('/[,|&]|\b and \b/', $value);
            if (! $authors) {
                $authors = explode(',', $value);
            }

            return array_map(static fn ($author) => trim($author), $authors);
        }

        return $value;
    }
}
