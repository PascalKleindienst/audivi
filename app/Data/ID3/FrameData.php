<?php

declare(strict_types=1);

namespace App\Data\ID3;

use App\Enums\ID3\FrameType;
use App\Enums\ID3\Genre;
use Spatie\LaravelData\Data;
use Stringable;

final class FrameData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly FrameType $type,
        public readonly Data|Genre|string|null $value
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        if (isset($data['value']) && $data['value'] instanceof Stringable) {
            $data['value'] = (string) $data['value'];
        }

        return new self(
            id: (string) $data['id'],
            type: $data['type'],
            value: $data['value'] ?? null,
        );
    }
}
