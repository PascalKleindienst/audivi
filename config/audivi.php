<?php

declare(strict_types=1);

return [
    'data_providers' => [
        'audible' => [
            'base_uri' => env('AUDIBLE_BASE_URI', 'https://api.audnex.us'),
            'timeout' => env('AUDIBLE_TIMEOUT', 10),
            'connect_timeout' => env('AUDIBLE_CONNECT_TIMEOUT', 2),
        ],
        'openlibrary' => [
            'base_uri' => env('OPENLIBRARY_BASE_URI', 'https://openlibrary.org'),
            'timeout' => env('OPENLIBRARY_TIMEOUT', 10),
            'connect_timeout' => env('OPENLIBRARY_CONNECT_TIMEOUT', 2),
        ],
    ],
];
