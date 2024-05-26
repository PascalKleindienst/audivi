<?php

use App\Http\Controllers\AudioBooksController;
use App\Http\Controllers\AuthorController;
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

    Route::get('/audio-books/{book}', [AudioBooksController::class, 'show'])->name('audio-books.show');
    Route::get('/audio-books', [AudioBooksController::class, 'index'])->name('audio-books');
    Route::post('/audio-books', [AudioBooksController::class, 'store'])->name('audio-books.store');
});

Route::get('/demo', static function () {
    return view('demo');
});
