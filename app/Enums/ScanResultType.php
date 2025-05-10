<?php

declare(strict_types=1);

namespace App\Enums;

enum ScanResultType: string
{
    case SUCCESS = 'success';
    case SKIPPED = 'skipped';
    case ERROR = 'error';
}
