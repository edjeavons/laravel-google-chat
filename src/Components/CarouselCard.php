<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Widgets\AbstractWidget;

class CarouselCard implements Arrayable
{
    protected array $payload = [];

    public function __construct(AbstractWidget|array $widgets = [])
    {
        $this->widgets($widgets);
    }

    public static function create(AbstractWidget|array $widgets = []): static
    {
        return new static($widgets);
    }

    public static function make(AbstractWidget|array $widgets = []): static
    {
        return new static($widgets);
    }

    public function widgets(AbstractWidget|array $widgets): static
    {
        foreach (Arr::wrap($widgets) as $widget) {
            if ($widget instanceof AbstractWidget) {
                $this->payload['widgets'][] = $widget->toArray();
            }
        }

        return $this;
    }

    public function addWidget(AbstractWidget $widget): static
    {
        $this->payload['widgets'][] = $widget->toArray();

        return $this;
    }

    public function footerWidgets(AbstractWidget|array $widgets): static
    {
        foreach (Arr::wrap($widgets) as $widget) {
            if ($widget instanceof AbstractWidget) {
                $this->payload['footerWidgets'][] = $widget->toArray();
            }
        }

        return $this;
    }

    public function addFooterWidget(AbstractWidget $widget): static
    {
        $this->payload['footerWidgets'][] = $widget->toArray();

        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
