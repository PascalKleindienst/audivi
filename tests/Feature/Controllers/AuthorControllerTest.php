<?php

use App\Models\AudioBook;
use App\Models\Author;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

beforeEach(function () {
    Author::factory()
        ->has(AudioBook::factory()->count(2))
        ->count(3)
        ->create();

    $user = User::factory()->create();

    actingAs($user);
});

it('is only available to logged in users', function () {
    \Illuminate\Support\Facades\Auth::logout();
    get(route('authors.index'))->assertRedirect(route('login'));
    get(route('authors.edit', 1))->assertRedirect(route('login'));
    get(route('authors.show', 1))->assertRedirect(route('login'));
    put(route('authors.update', 1))->assertRedirect(route('login'));

})->group('controllers', 'authors');

it('can list authors', function () {
    get(route('authors.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Author/Index')
            ->has('authors', fn(Assert $page) => $page
                ->has('meta', fn(Assert $page) => $page
                    ->where('current_page', 1)
                    ->where('last_page', 1)
                    ->where('from', 1)
                    ->where('to', 3)
                    ->where('total', 3)
                    ->where('per_page', 15)
                    ->where('prev_page_url', null)
                    ->has('next_page_url')
                    ->has('last_page_url')
                    ->has('first_page_url')
                    ->has('path')
                )
                ->has('links', 3)
                ->has('data', 3, fn(Assert $page) => $page
                    ->has('id')
                    ->has('name')
                    ->has('image')
                )
            )
        );
})->group('controllers', 'authors');

it('can show a specific author', function () {
    $author = Author::first();

    get(route('authors.show', $author->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Author/Show')
            ->has('author', fn(Assert $page) => $page
                ->whereAll($author->toArray())
            )
            ->has('books', 2, fn(Assert $page) => $page
                ->where('id',  $author->audioBooks->first()->id)
                ->where('title',  $author->audioBooks->first()->title)
                ->where('path',  $author->audioBooks->first()->path)
                ->etc()
            )
        );
})->group('controllers', 'authors');

it('can edit a specific author', function () {
    $author = Author::first();

    get(route('authors.edit', $author->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Author/Edit')
            ->has('author', fn(Assert $page) => $page
                ->whereAll($author->toArray())
            )
        );
})->group('controllers', 'authors');

it('can update an author', function () {
    Storage::fake();
    $author = Author::first();

    put(route('authors.update', $author->id), [
        'name' => 'Updated Name',
        'link' => 'Updated Link',
        'description' => 'Updated Description',
        'image' => null
    ]);

    $author->refresh();
    expect($author->name)->toBe('Updated Name')
        ->and($author->link)->toBe('Updated Link')
        ->and($author->description)->toBe('Updated Description')
        ->and($author->image)->toBeNull();
})->group('controllers', 'authors');

it('can update an author image', function () {
    Storage::fake();
    $author = Author::first();
    $file = UploadedFile::fake()->image('author.jpg');

    expect($author->image)->toBeNull();

    put(route('authors.update', $author->id), [
        'name' => 'Updated Name',
        'image' => $file
    ])
        ->assertRedirect(route('authors.show', $author->id))
        ->assertSessionHas('flash.bannerStyle', 'success')
        ->assertSessionHas('flash.banner')
    ;

    $author->refresh();
    expect($author->image)->not()->toBeNull();
    Storage::assertExists('public/authors/' . $author->image);
})->group('controllers', 'authors');
