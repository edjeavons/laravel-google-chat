<?php

namespace NotificationChannels\GoogleChat\Widgets;

use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Components\Button;

class ButtonList extends AbstractWidget
{
    public function __construct(Button|array $buttons = [])
    {
        $this->buttons($buttons);
    }

    public static function create(Button|array $buttons = []): static
    {
        return new static($buttons);
    }

    public static function make(Button|array $buttons = []): static
    {
        return new static($buttons);
    }

    public function buttons(Button|array $buttons): static
    {
        $buttons = Arr::wrap($buttons);

        foreach ($buttons as $button) {
            if ($button instanceof Button) {
                $this->payload['buttons'][] = $button->toArray();
            } elseif (is_array($button)) {
                $this->payload['buttons'][] = $button;
            }
        }

        return $this;
    }

    public function addButton(Button $button): static
    {
        $this->payload['buttons'][] = $button->toArray();

        return $this;
    }
}
