<?php

namespace NotificationChannels\GoogleChat\Components;

use Illuminate\Contracts\Support\Arrayable;
use NotificationChannels\GoogleChat\Enums\BorderType;
use NotificationChannels\GoogleChat\Enums\ImageCropStyle;
use NotificationChannels\GoogleChat\Enums\TextAlignment;

class GridItem implements Arrayable
{
    protected array $payload = [];

    public static function create(): static
    {
        return new static;
    }

    public static function make(): static
    {
        return new static;
    }

    public function image(string $imageUri): static
    {
        $this->payload['image']['imageUri'] = $imageUri;

        return $this;
    }

    public function cropStyle(ImageCropStyle|string $type): static
    {
        $this->payload['image']['cropStyle'] = [
            'type' => $type instanceof ImageCropStyle ? $type->value : $type,
        ];

        return $this;
    }

    public function borderStyle(BorderType|string $type): static
    {
        $this->payload['image']['borderStyle'] = [
            'type' => $type instanceof BorderType ? $type->value : $type,
        ];

        return $this;
    }

    public function title(string $title): static
    {
        $this->payload['title'] = $title;

        return $this;
    }

    public function subtitle(string $subtitle): static
    {
        $this->payload['subtitle'] = $subtitle;

        return $this;
    }

    public function textAlignment(TextAlignment|string $alignment): static
    {
        $this->payload['textAlignment'] = $alignment instanceof TextAlignment ? $alignment->value : $alignment;

        return $this;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
