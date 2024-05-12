<?php

declare(strict_types=1);

namespace App\Install;

use Exception;

class InstallException extends Exception
{
    public function __construct(string $message, int $code = 0, ?Exception $previous = null, public readonly ?InstallationStep $step = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
