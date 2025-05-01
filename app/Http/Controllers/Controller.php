<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\BreadcrumbItemData;
use Inertia\Inertia;

abstract class Controller
{
    protected function withBreadcrumbs(BreadcrumbItemData ...$breadcrumbs): void
    {
        Inertia::share('breadcrumbs', $breadcrumbs);
    }
}
