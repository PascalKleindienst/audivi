<?php

declare(strict_types=1);

namespace App\Data\Library;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

final class ItemData extends Data
{
    #[Computed]
    public readonly ?int $size;

    public function __construct(
        public readonly string $folder,
        /** @var Collection<int|string, string> */
        public readonly Collection $files,
        public readonly MetaData $meta,
    ) {
        $this->size = $files->sum(static fn (string $path) => Storage::disk('library')->size($path));
    }
}
