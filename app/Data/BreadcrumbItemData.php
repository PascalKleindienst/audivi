<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class BreadcrumbItemData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $url = null,
    ) {}

    public static function fromString(string $title): self
    {
        return new self($title);
    }

    public static function fromMultiple(string $title, ?string $url): self
    {
        return new self($title, $url);
    }
}
