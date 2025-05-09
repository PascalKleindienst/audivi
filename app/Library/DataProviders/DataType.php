<?php

declare(strict_types=1);

namespace App\Library\DataProviders;

enum DataType: string
{
    case AUTHOR = 'author';
    case BOOK = 'book';
}
