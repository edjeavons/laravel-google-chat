<?php

namespace NotificationChannels\GoogleChat\Transports;

use NotificationChannels\GoogleChat\GoogleChatMessage;

interface TransportInterface
{
    /**
     * Send or update a Google Chat message.
     */
    public function send(string $endpoint, GoogleChatMessage $message): mixed;
}
