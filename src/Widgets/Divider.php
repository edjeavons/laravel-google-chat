<?php

namespace NotificationChannels\GoogleChat\Widgets;

class Divider extends AbstractWidget
{
    public static function create(): static
    {
        return new static;
    }

    public static function make(): static
    {
        return new static;
    }

    public function toArray(): array
    {
        return [
            'divider' => (object) [],
        ];
    }
}
