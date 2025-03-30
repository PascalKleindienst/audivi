<?php

declare(strict_types=1);

namespace App\Scanners;

use App\ValueObjects\Buffer;
use App\ValueObjects\Version;
use Spatie\LaravelData\Data;

/**
 * @template T of Data
 */
interface Parser
{
    public function check(Version $version, Buffer $buffer): bool;

    /**
     * @phpstan-return T
     * @throws ParserError
     */
    public function parse(Buffer $buffer): Data;
}
