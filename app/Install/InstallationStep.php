<?php

declare(strict_types=1);

namespace App\Install;

enum InstallationStep
{
    case COMPOSER_INSTALL;
    case CHECK_REQUIREMENTS;

    case SETUP_ENVIRONMENT;

    case CONFIGURE_DATABASE;

    case UPDATE_ENVIRONMENT;

    case CREATE_DATABASE;

    case CREATE_ADMIN;

    case BUILD_ASSETS;

    case CLEANUP;
}
