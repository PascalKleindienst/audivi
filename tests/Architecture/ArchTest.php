<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Spatie\LaravelData\Data;

arch('php')->preset()->php();
arch('laravel')->preset()->laravel()->ignoring([
    AppServiceProvider::class,
    'App\\Install\\',
]);
arch('security')->preset()->security()->ignoring([
    'App\\Install\\',
]);

arch('DTOs')->expect('App')
    ->not->toExtend(Data::class)
    ->ignoring('App\Data');

arch('actions')->expect('App\Actions')
    ->classes()
    ->toBeFinal()
    ->ignoring(['App\\Actions\\Fortify', 'App\\Actions\\Jetstream'])
    ->toHaveMethod('handle')
    ->ignoring(['App\\Actions\\Fortify', 'App\\Actions\\Jetstream'])
    ->toHaveSuffix('Action')
    ->ignoring(['App\\Actions\\Fortify', 'App\\Actions\\Jetstream'])
    ->toBeReadonly()
    ->ignoring(['App\\Actions\\Fortify', 'App\\Actions\\Jetstream']);
