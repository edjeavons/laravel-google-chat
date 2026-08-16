<?php

namespace NotificationChannels\GoogleChat\Widgets;

use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Components\Chip;
use NotificationChannels\GoogleChat\Enums\ChipListLayout;

class ChipList extends AbstractWidget
{
    public function __construct(Chip|array $chips = [])
    {
        $this->chips($chips);
    }

    public static function create(Chip|array $chips = []): static
    {
        return new static($chips);
    }

    public static function make(Chip|array $chips = []): static
    {
        return new static($chips);
    }

    public function chips(Chip|array $chips): static
    {
        foreach (Arr::wrap($chips) as $chip) {
            if ($chip instanceof Chip) {
                $this->payload['chips'][] = $chip->toArray();
            } elseif (is_array($chip)) {
                $this->payload['chips'][] = $chip;
            }
        }

        return $this;
    }

    public function addChip(Chip $chip): static
    {
        $this->payload['chips'][] = $chip->toArray();

        return $this;
    }

    /**
     * Set the chip list layout.
     */
    public function layout(ChipListLayout|string $layout): static
    {
        $this->payload['layout'] = $layout instanceof ChipListLayout ? $layout->value : $layout;

        return $this;
    }

    /**
     * Wrap chips onto a new line when needed.
     */
    public function wrapped(): static
    {
        return $this->layout(ChipListLayout::WRAPPED);
    }

    /**
     * Scroll chips horizontally when they do not fit.
     */
    public function horizontalScrollable(): static
    {
        return $this->layout(ChipListLayout::HORIZONTAL_SCROLLABLE);
    }
}
