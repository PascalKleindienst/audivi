<?php

declare(strict_types=1);

namespace App\Data\Library;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use SplFileInfo;

final class ItemData extends Data
{
    public const AUDIO_TYPES = ['mp3', 'm4a'];

    /**
     * @var Collection<SplFileInfo> $mediaFiles
     */
    #[Computed]
    public readonly Collection $mediaFiles;

    #[Computed]
    public readonly ?int $size;

    public function __construct(
        public readonly string $folder,
        /** @var Collection<SplFileInfo> */
        public readonly Collection $files,
        public readonly MetaData $meta,
    ) {
        $this->mediaFiles = $files->filter(static fn (SplFileInfo $file) => \in_array(
            $file->getExtension(),
            self::AUDIO_TYPES,
            true
        ));

        $this->size = $files->sum(static fn (SplFileInfo $file) => $file->getSize());
    }
}
