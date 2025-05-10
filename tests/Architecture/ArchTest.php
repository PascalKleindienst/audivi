<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;

arch()->preset()->php();
arch()->preset()->laravel()->ignoring([
    AppServiceProvider::class,
    'App\\Install\\',
]);
arch()->preset()->security()->ignoring([
    'App\\Install\\',
]);
