<?php

declare(strict_types=1);

use App\Http\Controllers\AudioBooksController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', static function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', static function (\Illuminate\Http\Request $request) {
        $request->session()->flash('flash.banner', 'Yay it works!');
        $request->session()->flash('flash.bannerStyle', 'success');

        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('authors', AuthorController::class)->except(['create', 'destroy', 'store']);
    Route::post('authors/{author}/metadata', [AuthorController::class, 'metadata'])->name('authors.metadata');
    Route::resource('series', SeriesController::class)->except(['create', 'destroy', 'store']);

    Route::post('/playlist/update', [PlaylistController::class, 'update'])->name('playlist.update');
    Route::get('/playlist/play/{track}', [PlaylistController::class, 'play'])->name('playlist.play');

    Route::get('/audio-books/{book}', [AudioBooksController::class, 'show'])->name('audio-books.show');
    Route::get('/audio-books', [AudioBooksController::class, 'index'])->name('audio-books');
    Route::post('/audio-books', [AudioBooksController::class, 'store'])->name('audio-books.store');

    Route::prefix('admin/')->name('admin.')->middleware('can:admin')->group(function () {
        Route::resource('users', UserController::class, ['except' => ['show']]);
    });
});
