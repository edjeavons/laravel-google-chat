<?php

namespace NotificationChannels\GoogleChat\Widgets;

use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Components\MaterialIcon;
use NotificationChannels\GoogleChat\Enums\Icon;

class DecoratedText extends AbstractWidget
{
    public function __construct(?string $text = null)
    {
        if ($text !== null) {
            $this->text($text);
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

    public function text(string $text): static
    {
        $this->payload['text'] = $text;

        return $this;
    }

    public function topLabel(string $topLabel): static
    {
        $this->payload['topLabel'] = $topLabel;

        return $this;
    }

    public function bottomLabel(string $bottomLabel): static
    {
        $this->payload['bottomLabel'] = $bottomLabel;

        return $this;
    }

    public function wrapText(bool $wrapText = true): static
    {
        $this->payload['wrapText'] = $wrapText;

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

    public function endIcon(Icon|MaterialIcon|string $icon): static
    {
        if ($icon instanceof Icon) {
            $this->payload['endIcon'] = ['knownIcon' => $icon->value];
        } elseif ($icon instanceof MaterialIcon) {
            $this->payload['endIcon'] = $icon->toArray();
        } elseif (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            $this->payload['endIcon'] = ['iconUrl' => $icon];
        } else {
            $this->payload['endIcon'] = ['knownIcon' => (string) $icon];
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

    public function button(Button $button): static
    {
        $this->payload['button'] = $button->toArray();

        return $this;
    }
}
