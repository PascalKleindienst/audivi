<?php

declare(strict_types=1);

namespace App\Scanners\ID3;

use Illuminate\Support\Facades\Facade;

/**
 * @see ParserService
 */
final class Parser extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'id3.parser';
    }
}
