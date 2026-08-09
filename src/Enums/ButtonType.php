<?php

namespace NotificationChannels\GoogleChat\Enums;

enum ButtonType: string
{
    case OUTLINED = 'OUTLINED';
    case FILLED = 'FILLED';
    case FILLED_TONAL = 'FILLED_TONAL';
    case BORDERLESS = 'BORDERLESS';
}
