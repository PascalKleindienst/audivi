<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\BreadcrumbItemData;
use App\Data\SeriesData;
use App\Models\Series;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

final class SeriesController extends Controller
{
    public function index(): Response
    {
        $series = SeriesData::collect(
            Series::query()->with(['audioBooks' => fn ($query) => $query->limit(4)])->orderBy('name')->paginate()->withQueryString(),
            PaginatedDataCollection::class
        )->include('books')->exclude('books.tracks');

        $this->withBreadcrumbs(BreadcrumbItemData::from(trans('navigation.series'), route('series.index')));

        return Inertia::render('Series/Index', ['series' => $series]);
    }

    public function show(Series $series): Response
    {
        $series->loadMissing(['audioBooks' => fn ($query) => $query->orderByRaw('COALESCE(volume, title) asc'), 'audioBooks.authors']);

        $this->withBreadcrumbs(
            BreadcrumbItemData::from(trans('navigation.series'), route('series.index')),
            BreadcrumbItemData::from($series->name, route('series.show', $series)),
        );

        return Inertia::render('Series/Show', [
            'series' => SeriesData::from($series)->include('books.authors'),
        ]);
    }
}
