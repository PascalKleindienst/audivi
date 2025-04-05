<?php

declare(strict_types=1);

namespace App\Events;

use App\Data\Library\ScanResultData;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class ScanProgressEvent
{
    use Dispatchable;

    public function __construct(public ScanResultData $data)
    {
    }
}
