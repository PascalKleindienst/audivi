<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class TrackData extends Data
{
    public function __construct(
        #[LiteralTypeScriptType('number')]
        public readonly ?int $id,
        public readonly string $title,
        public readonly int $position,
        public readonly ?string $path = null,
        public readonly ?float $start = null,
        public readonly ?float $end = null,
        public readonly ?int $duration = null,
        public readonly int $mTime = 0
    ) {}
}
