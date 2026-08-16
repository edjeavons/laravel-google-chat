<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;

class CardAction implements Arrayable
{
    protected array $payload = [];

    public function __construct(string $actionLabel, ?string $openUrl = null)
    {
        $this->actionLabel($actionLabel);
        if ($openUrl) {
            $this->openUrl($openUrl);
        }
    }

    public static function make(string $actionLabel, ?string $openUrl = null): static
    {
        return new static($actionLabel, $openUrl);
    }

    public function actionLabel(string $actionLabel): static
    {
        $this->payload['actionLabel'] = $actionLabel;

        return $this;
    }

    public function openUrl(string $url): static
    {
        $this->payload['onClick'] = [
            'openLink' => [
                'url' => $url,
            ],
        ];

        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
