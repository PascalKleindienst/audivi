<?php

declare(strict_types=1);

namespace App\Providers;

use App\Scanners\ID3\ID3TagScanner;
use App\Scanners\ID3\ParserService;
use App\Scanners\Scanner;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind('id3.parser', static fn (Application $app) => $app->make(ParserService::class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->resolving(Scanner::class, function (Scanner $scanner, Application $app) {
            $scanner->addScanner($app->get(ID3TagScanner::class));
        });
    }
}
