<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\AudioBookData;
use App\Data\AuthorData;
use App\Data\BreadcrumbItemData;
use App\Enums\DataProviderType;
use App\Facades\DataProvider;
use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

final class AuthorController extends Controller
{
    public function index(): Response
    {
        $authors = AuthorData::collect(
            Author::query()->orderBy('name')->paginate()->withQueryString(),
            PaginatedDataCollection::class
        )->include('image');

        $this->withBreadcrumbs(BreadcrumbItemData::from(trans('navigation.authors'), route('authors.index')));

        return Inertia::render('Author/Index', ['authors' => $authors]);
    }

    public function show(Author $author): Response
    {
        $author->load(['audioBooks' => fn ($query) => $query->limit(10), 'audioBooks.tracks']);

        $this->withBreadcrumbs(
            BreadcrumbItemData::from(trans('navigation.authors'), route('authors.index')),
            BreadcrumbItemData::from($author->name, $author->link),
        );

        return Inertia::render('Author/Show', [
            'author' => AuthorData::from($author)->include('*'),
            'books' => AudioBookData::collect($author->audioBooks, DataCollection::class)->except('description'),
        ]);
    }

    public function edit(Author $author): Response
    {
        $this->withBreadcrumbs(
            BreadcrumbItemData::from(trans('navigation.authors'), route('authors.index')),
            BreadcrumbItemData::from($author->name, $author->link),
            BreadcrumbItemData::from(trans('general.edit'))
        );

        return Inertia::render('Author/Edit', [
            'author' => AuthorData::from($author)->include('*'),
            'providers' => DataProvider::providers(DataProviderType::AUTHOR),
            'defaultProvider' => DataProvider::getDefaultDriver(),
        ]);
    }

    public function update(Author $author, AuthorData $data): RedirectResponse
    {
        if ($data->image instanceof UploadedFile) {
            if ($author->image) {
                Storage::disk('public')->delete('authors/'.$author->image);
            }

            $imageName = time().'.'.$data->image->getClientOriginalName();
            $data->image->storeAs('public/authors', $imageName);
        }

        $author->fill([
            'name' => $data->name,
            'description' => $data->description,
            'link' => $data->link,
            'image' => $imageName ?? $author->image,
        ])->save();

        return Redirect::route('authors.show', $author->id)
            ->banner(__('author.updated.success'));
    }
}
