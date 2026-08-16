<?php

namespace NotificationChannels\GoogleChat\Tests\Fixtures;

use Illuminate\Notifications\Notifiable;

class TestNotifiable
{
    use Notifiable;

    public function __construct(private ?string $space = null) {}

    public function routeNotificationForGoogleChat(): ?string
    {
        return $this->space;
    }
}
