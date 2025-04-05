<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

final readonly class CollectedScanPathsEvent
{
    use Dispatchable;

    /**
     * @param  Collection<string, Collection<int, string>>  $paths
     */
    public function __construct(public Collection $paths)
    {
    }
}
