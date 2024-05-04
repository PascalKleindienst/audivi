<?php

declare(strict_types=1);

namespace App\Data\ID3;

enum ImageType: int
{
    case OTHER = 0;
    case FILE_ICON = 1;
    case ICON = 2;
    case COVER_FRONT = 3;
    case COVER_BACK = 4;
    case LEAFLET = 5;
    case MEDIA = 6;
    case ARTIST_LEAD = 7;
    case ARTIST = 8;
    case CONDUCTOR = 9;
    case BAND = 10;
    case COMPOSER = 11;
    case WRITER = 12;
    case LOCATION = 13;
    case DURING_RECORDING = 14;
    case DURING_PERFORMANCE = 15;
    case SCREEN = 16;
    case FISH = 17;
    case ILLUSTRATION = 18;
    case LOGO_BAND = 19;
    case LOGO_PUBLISHER = 20;
}
