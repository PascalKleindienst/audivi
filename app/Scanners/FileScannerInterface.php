<?php

declare(strict_types=1);

namespace App\Scanners;

use App\Data\Library\ItemData;
use App\Data\Library\MetaData;

interface FileScannerInterface
{
    public function scanItem(ItemData $item): MetaData;
}
