<?php

namespace NotificationChannels\GoogleChat\Enums;

enum SelectionType: string
{
    case CHECK_BOX = 'CHECK_BOX';
    case RADIO_BUTTON = 'RADIO_BUTTON';
    case SWITCH = 'SWITCH';
    case DROPDOWN = 'DROPDOWN';
}
