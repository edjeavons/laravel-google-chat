<?php

namespace NotificationChannels\GoogleChat;

use Exception;
use Illuminate\Notifications\Notification;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\Transports\ServiceAccountTransport;
use NotificationChannels\GoogleChat\Transports\TransportInterface;
use NotificationChannels\GoogleChat\Transports\WebhookTransport;

class GoogleChatChannel
{
    /**
     * Initialise a new Google Chat Channel instance.
     */
    public function __construct(protected ?TransportInterface $transport = null) {}

    /**
     * Send the given notification.
     *
     * @throws CouldNotSendNotification
     */
    public function send(mixed $notifiable, Notification $notification): mixed
    {
        if (! method_exists($notification, 'toGoogleChat')) {
            throw CouldNotSendNotification::undefinedMethod($notification);
        }

        $message = $notification->toGoogleChat($notifiable);

        if (! $message instanceof GoogleChatMessage) {
            throw CouldNotSendNotification::invalidMessage($message);
        }

        $space = config('google-chat.test_space')
            ?? $message->getSpace()
            ?? (is_object($notifiable) && method_exists($notifiable, 'routeNotificationFor') ? $notifiable->routeNotificationFor('googleChat') : null)
            ?? config('google-chat.space');

        if (! $endpoint = config("google-chat.spaces.$space", $space)) {
            throw CouldNotSendNotification::webhookUnavailable();
        }

        $transport = $this->transport ?? $this->resolveTransport();

        try {
            return $transport->send($endpoint, $message);
        } catch (CouldNotSendNotification $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw CouldNotSendNotification::unexpectedException($exception);
        }
    }

    /**
     * Resolve the transport driver based on configuration.
     */
    protected function resolveTransport(): TransportInterface
    {
        $driver = config('google-chat.driver', 'webhook');

        return match ($driver) {
            'service_account' => new ServiceAccountTransport,
            default => new WebhookTransport,
        };
    }
}
