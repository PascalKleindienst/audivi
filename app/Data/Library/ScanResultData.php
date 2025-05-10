<?php

declare(strict_types=1);

namespace App\Data\Library;

use App\Enums\ScanResultType;
use Spatie\LaravelData\Data;

final class ScanResultData extends Data
{
    public function __construct(
        public string $path,
        public ScanResultType $result = ScanResultType::SUCCESS,
        public ?string $error = null
    ) {}

    public static function success(string $path): self
    {
        return new self($path, ScanResultType::SUCCESS);
    }

    public static function skipped(string $path): self
    {
        return new self($path, ScanResultType::SKIPPED, null);
    }

    public static function error(string $path, ?string $error = null): self
    {
        return new self($path, ScanResultType::ERROR, $error);
    }
}
