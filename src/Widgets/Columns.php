<?php

namespace NotificationChannels\GoogleChat\Widgets;

use Closure;
use NotificationChannels\GoogleChat\Enums\HorizontalAlignment;
use NotificationChannels\GoogleChat\Enums\HorizontalSizeStyle;
use NotificationChannels\GoogleChat\Enums\VerticalAlignment;

class Columns extends AbstractWidget
{
    protected array $columnItems = [];

    public static function create(): static
    {
        return new static;
    }

    public static function make(): static
    {
        return new static;
    }

    public function column(
        array|Closure $widgets,
        HorizontalSizeStyle|string|null $horizontalSizeStyle = null,
        HorizontalAlignment|string|null $horizontalAlignment = null,
        VerticalAlignment|string|null $verticalAlignment = null,
    ): static {
        if ($widgets instanceof Closure) {
            $columnBuilder = new class
            {
                public array $widgets = [];

                public function add(AbstractWidget $widget): static
                {
                    $this->widgets[] = $widget->toArray();

                    return $this;
                }
            };
            $widgets($columnBuilder);
            $widgetsList = $columnBuilder->widgets;
        } else {
            $widgetsList = array_map(function ($widget) {
                return $widget instanceof AbstractWidget ? $widget->toArray() : $widget;
            }, $widgets);
        }

        $column = [
            'widgets' => $widgetsList,
        ];

        if ($horizontalSizeStyle !== null) {
            $column['horizontalSizeStyle'] = $horizontalSizeStyle instanceof HorizontalSizeStyle
                ? $horizontalSizeStyle->value
                : $horizontalSizeStyle;
        }

        if ($horizontalAlignment !== null) {
            $column['horizontalAlignment'] = $horizontalAlignment instanceof HorizontalAlignment
                ? $horizontalAlignment->value
                : $horizontalAlignment;
        }

        if ($verticalAlignment !== null) {
            $column['verticalAlignment'] = $verticalAlignment instanceof VerticalAlignment
                ? $verticalAlignment->value
                : $verticalAlignment;
        }

        $this->columnItems[] = $column;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'columns' => [
                'columnItems' => $this->columnItems,
            ],
        ];
    }
}
