<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;
use NotificationChannels\GoogleChat\Enums\Icon;

class Chip implements Arrayable
{
    protected array $payload = [];

    public function __construct(?string $label = null)
    {
        if ($label !== null) {
            $this->labelContent($label);
        }
    }

    public static function create(?string $label = null): static
    {
        return new static($label);
    }

    public static function make(?string $label = null): static
    {
        return new static($label);
    }

    public static function label(string $label): static
    {
        return new static($label);
    }

    public function labelContent(string $label): static
    {
        $this->payload['label'] = $label;

        return $this;
    }

    public function icon(Icon|MaterialIcon|string $icon): static
    {
        if ($icon instanceof Icon) {
            $this->payload['icon'] = ['knownIcon' => $icon->value];
        } elseif ($icon instanceof MaterialIcon) {
            $this->payload['icon'] = $icon->toArray();
        } elseif (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            $this->payload['icon'] = ['iconUrl' => $icon];
        } else {
            $this->payload['icon'] = ['knownIcon' => $icon];
        }

        return $this;
    }

    /**
     * Set alternative text for the chip icon.
     */
    public function iconAltText(string $altText): static
    {
        $this->payload['icon']['altText'] = $altText;

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

    public function disabled(bool $disabled = true): static
    {
        $this->payload['disabled'] = $disabled;

        return $this;
    }

    public function altText(string $altText): static
    {
        $this->payload['altText'] = $altText;

        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
