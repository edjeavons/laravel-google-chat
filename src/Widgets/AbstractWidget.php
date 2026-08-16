<?php

namespace NotificationChannels\GoogleChat\Widgets;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

abstract class AbstractWidget implements Arrayable
{
    /**
     * The widget payload.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * Serialize the widget to an array representation.
     */
    public function toArray(): array
    {
        $widgetName = Str::camel(class_basename(static::class));

        return [
            $widgetName => $this->payload,
        ];
    }
}
