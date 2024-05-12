<?php

use App\Install\Composer;
use App\Install\InstallationStep;
use App\Install\InstallException;
use App\Install\Requirement;
use App\Install\RequirementsChecker;

test('composer and requirements checker are isolated')
    ->expect([Composer::class, RequirementsChecker::class])
    ->toUseNothing()
    ->ignoring(InstallException::class)
    ->ignoring(InstallationStep::class)
    ->ignoring(Requirement::class)
    ->ignoring(JsonException::class);
