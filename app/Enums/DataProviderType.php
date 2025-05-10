<?php

declare(strict_types=1);

namespace App\Enums;

enum DataProviderType: string
{
    case AUTHOR = 'author';
    case BOOK = 'book';
}
