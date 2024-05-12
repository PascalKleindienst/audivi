<?php

declare(strict_types=1);

namespace App\Install;

final class Permission
{
    public function __construct(public string $name, public string $permission, public bool $valid)
    {
    }

    public static function checkIsWriteable(string $name, string $permission): self
    {
        return new self($name, '775', \is_writable($permission));
    }
}
