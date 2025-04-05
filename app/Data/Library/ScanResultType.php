<?php

declare(strict_types=1);

namespace App\Data\Library;

enum ScanResultType: string
{
    case SUCCESS = 'success';
    case SKIPPED = 'skipped';
    case ERROR = 'error';
}
