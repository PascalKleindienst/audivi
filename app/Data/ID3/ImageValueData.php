<?php

declare(strict_types=1);

namespace App\Data\ID3;

use Spatie\LaravelData\Data;

final class ImageValueData extends Data
{
    public function __construct(
        public readonly ImageType $type,
        public readonly string $mime,
        public readonly ?string $description,
        public readonly string $data
    ) {
    }
}
