<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;
use NotificationChannels\GoogleChat\Enums\Icon;

class OverflowMenuItem implements Arrayable
{
    protected array $payload = [];

    public function __construct(?string $text = null)
    {
        if ($text !== null) {
            $this->textContent($text);
        }
    }

    public static function create(?string $text = null): static
    {
        return new static($text);
    }

    public static function make(?string $text = null): static
    {
        return new static($text);
    }

    public static function text(string $text): static
    {
        return new static($text);
    }

    public function textContent(string $text): static
    {
        $this->payload['text'] = $text;

        return $this;
    }

    public function startIcon(Icon|MaterialIcon|string $icon): static
    {
        if ($icon instanceof Icon) {
            $this->payload['startIcon'] = ['knownIcon' => $icon->value];
        } elseif ($icon instanceof MaterialIcon) {
            $this->payload['startIcon'] = $icon->toArray();
        } elseif (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            $this->payload['startIcon'] = ['iconUrl' => $icon];
        } else {
            $this->payload['startIcon'] = ['knownIcon' => (string) $icon];
        }

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

    public function onClickAction(string $function, array $parameters = []): static
    {
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[] = [
                'key' => (string) $key,
                'value' => (string) $value,
            ];
        }

        $this->payload['onClick'] = [
            'action' => [
                'function' => $function,
                'parameters' => $params,
            ],
        ];

        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
