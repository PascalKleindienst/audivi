<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\AudioBookData;
use App\Data\AuthorData;
use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class AuthorController extends Controller
{
    public function index(): Response
    {
        $authors = AuthorData::collect(
            Author::query()->orderBy('name')->paginate()->withQueryString(),
            PaginatedDataCollection::class
        )->include('image');

        return Inertia::render('Author/Index', ['authors' => $authors]);
    }

    public function show(int $id): Response
    {
        $author = Author::with(['audioBooks' => fn($query) => $query->limit(10)])->findOrFail($id);

        return Inertia::render('Author/Show', [
            'author' => AuthorData::from($author)->include('*'),
            'books' =>  AudioBookData::collect($author->audioBooks, DataCollection::class)->except('description')
        ]);
    }

    public function edit(Author $author): Response
    {
        return Inertia::render('Author/Edit', ['author' => AuthorData::from($author)->include('*')]);
    }

    public function update(Author $author, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'link' => ['string', 'nullable'],
            'description' => ['string', 'nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif', 'max:2048']
        ]);

        /** @var UploadedFile|null $image */
        $image = $validated['image'];

        if ($image) {
            if ($author->image) {
                Storage::disk('public')->delete('authors/' . $author->image);
            }

            $imageName = time() . '.' . $image->getClientOriginalName();
            $image->storeAs('public/authors', $imageName);
        }

        $author->name = $validated['name'];
        $author->link = $validated['link'] ?? $author->link;
        $author->description = $validated['description'] ?? $author->description;
        $author->image = $imageName ?? $author->image;
        $author->save();

        return Redirect::route('authors.show', $author->id)
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', __('author.updated.success'));
    }
}
