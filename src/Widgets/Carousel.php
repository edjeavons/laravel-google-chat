<?php

namespace NotificationChannels\GoogleChat\Widgets;

use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Components\CarouselCard;

class Carousel extends AbstractWidget
{
    public function __construct(CarouselCard|array $cards = [])
    {
        $this->cards($cards);
    }

    public static function create(CarouselCard|array $cards = []): static
    {
        return new static($cards);
    }

    public static function make(CarouselCard|array $cards = []): static
    {
        return new static($cards);
    }

    public function cards(CarouselCard|array $cards): static
    {
        foreach (Arr::wrap($cards) as $card) {
            if ($card instanceof CarouselCard) {
                $this->payload['carouselCards'][] = $card->toArray();
            } elseif (is_array($card)) {
                $this->payload['carouselCards'][] = $card;
            }
        }

        return $this;
    }

    public function addCard(CarouselCard $card): static
    {
        $this->payload['carouselCards'][] = $card->toArray();

        return $this;
    }
}
