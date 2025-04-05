<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;
use App\Data\Library\ScanResultData;

interface FileScannerInterface
{
    public function setItem(ItemData $item): void;

    public function scan(string $file): ScanResultData;

    public function prepareScanResult(): MetaData;
}
