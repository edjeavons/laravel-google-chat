<?php

namespace NotificationChannels\GoogleChat\Widgets;

use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Components\GridItem;

class Grid extends AbstractWidget
{
    public function __construct(GridItem|array $items = [])
    {
        $this->items($items);
    }

    public static function create(GridItem|array $items = []): static
    {
        return new static($items);
    }

    public static function make(GridItem|array $items = []): static
    {
        return new static($items);
    }

    public function title(string $title): static
    {
        $this->payload['title'] = $title;

        return $this;
    }

    public function columnCount(int $columnCount): static
    {
        $this->payload['columnCount'] = $columnCount;

        return $this;
    }

    public function items(GridItem|array $items): static
    {
        foreach (Arr::wrap($items) as $item) {
            if ($item instanceof GridItem) {
                $this->payload['items'][] = $item->toArray();
            } elseif (is_array($item)) {
                $this->payload['items'][] = $item;
            }
        }

        return $this;
    }

    public function addItem(GridItem $item): static
    {
        $this->payload['items'][] = $item->toArray();

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
}
