<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class Version
{
    private function __construct(public int $major, public int $minor)
    {
    }

    public static function from(int $major, int $minor): self
    {
        if ($major < 0 || $major > 255 || $minor < 0 || $minor > 255) {
            throw new \InvalidArgumentException("Invalid version: {$major}.{$minor}");
        }

        return new self($major, $minor);
    }
}
