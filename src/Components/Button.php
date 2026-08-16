<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use NotificationChannels\GoogleChat\Enums\ButtonType;
use NotificationChannels\GoogleChat\Enums\Icon;

class Button implements Arrayable
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

    public function icon(Icon|MaterialIcon|string $icon): static
    {
        if ($icon instanceof Icon) {
            $this->payload['icon'] = ['knownIcon' => $icon->value];
        } elseif ($icon instanceof MaterialIcon) {
            $this->payload['icon'] = $icon->toArray();
        } elseif (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            $this->payload['icon'] = ['iconUrl' => $icon];
        } else {
            $this->payload['icon'] = ['knownIcon' => (string) $icon];
        }

        return $this;
    }

    /**
     * Set alternative text for the button icon.
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

    /**
     * Display an overflow menu instead of a direct click action.
     */
    public function overflowMenu(OverflowMenuItem|array $items): static
    {
        if ($items instanceof OverflowMenuItem) {
            $items = [$items];
        }

        $this->payload['onClick'] = [
            'overflowMenu' => [
                'items' => array_map(
                    fn ($item) => $item instanceof OverflowMenuItem ? $item->toArray() : $item,
                    $items,
                ),
            ],
        ];

        return $this;
    }

    /**
     * Set the visual type of the button.
     */
    public function type(ButtonType|string $type): static
    {
        $this->payload['type'] = $type instanceof ButtonType ? $type->value : $type;

        return $this;
    }

    /**
     * Render the button with an outlined container.
     */
    public function outlined(): static
    {
        return $this->type(ButtonType::OUTLINED);
    }

    /**
     * Render the button with a filled container.
     */
    public function filled(): static
    {
        return $this->type(ButtonType::FILLED);
    }

    /**
     * Render the button with a filled tonal container.
     */
    public function filledTonal(): static
    {
        return $this->type(ButtonType::FILLED_TONAL);
    }

    /**
     * Render the button without a container.
     */
    public function borderless(): static
    {
        return $this->type(ButtonType::BORDERLESS);
    }

    /**
     * Set the button colour using normalised RGBA components.
     *
     * Google Chat renders coloured buttons as FILLED, regardless of their type.
     */
    public function color(float $red, float $green, float $blue, ?float $alpha = null): static
    {
        foreach ([$red, $green, $blue, $alpha] as $component) {
            if ($component === null) {
                continue;
            }

            if ($component < 0 || $component > 1) {
                throw new InvalidArgumentException('Button colour components must be between 0 and 1.');
            }
        }

        $this->payload['color'] = compact('red', 'green', 'blue');

        if ($alpha !== null) {
            $this->payload['color']['alpha'] = $alpha;
        }

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
