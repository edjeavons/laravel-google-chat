<?php

namespace NotificationChannels\GoogleChat\Transports;

use Illuminate\Support\Facades\Http;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\GoogleChatMessage;

class WebhookTransport implements TransportInterface
{
    public function send(string $endpoint, GoogleChatMessage $message): mixed
    {
        $url = $endpoint;

        $replyOption = $message->getReplyOption();
        if (! $replyOption && $message->isThreaded()) {
            $replyOption = 'REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD';
        }

        if ($replyOption) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator.'messageReplyOption='.$replyOption;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=UTF-8',
        ])->withOptions([
            'curl' => [CURLOPT_FORBID_REUSE => true],
        ])->post($url, $message->toArray());

        if ($response->failed()) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

        return $response->json();
    }
}
