<?php

declare(strict_types=1);

namespace App\Install;

final class Requirement
{
    public function __construct(public string $name, public string $version, public string $current, public bool $valid)
    {
    }
}
