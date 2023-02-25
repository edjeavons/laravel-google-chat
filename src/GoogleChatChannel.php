<?php

namespace NotificationChannels\GoogleChat;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Notifications\Notification;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;

class GoogleChatChannel
{
    /**
     * The Http Client.
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Initialise a new Google Chat Channel instance.
     *
     * @param \GuzzleHttp\Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toGoogleChat')) {
            throw CouldNotSendNotification::undefinedMethod($notification);
        }

        /** @var \NotificationChannels\GoogleChat\GoogleChatMessage $message */
        if (! ($message = $notification->toGoogleChat($notifiable)) instanceof GoogleChatMessage) {
            throw CouldNotSendNotification::invalidMessage($message);
        }

        $space = $message->getSpace()
            ?? $notifiable->routeNotificationFor('googleChat')
            ?? config('google-chat.space');

        if (! $endpoint = config("google-chat.spaces.$space", $space)) {
            throw CouldNotSendNotification::webhookUnavailable();
        }

        if ($message->isThreaded()) {
            $endpoint .= '&messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD';
        }

        try {
            $this->client->request(
                'post',
                $endpoint,
                [
                    'json' => $message->toArray(),
                ]
            );
        } catch (ClientException $exception) {
            throw CouldNotSendNotification::clientError($exception);
        } catch (Exception $exception) {
            throw CouldNotSendNotification::unexpectedException($exception);
        }

        return $this;
    }
}
