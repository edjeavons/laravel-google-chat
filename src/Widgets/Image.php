<?php

namespace NotificationChannels\GoogleChat\Widgets;

class Image extends AbstractWidget
{
    /**
     * Set the image url.
     */
    public function imageUrl(string $url): static
    {
        $this->payload['imageUrl'] = $url;

        return $this;
    }

    /**
     * Make the widget clickable through to the provided link.
     */
    public function onClick(string $url): static
    {
        $this->payload['onClick'] = [
            'openLink' => [
                'url' => $url,
            ],
        ];

        return $this;
    }

    /**
     * Set the alternative text for accessibility.
     */
    public function altText(string $altText): static
    {
        $this->payload['altText'] = $altText;

        return $this;
    }

    /**
     * Make the widget clickable through to the provided link.
     */
    public function openUrl(string $url): static
    {
        return $this->onClick($url);
    }

    /**
     * Return a new Image widget instance.
     */
    public static function create(?string $imageUrl = null, ?string $onClickUrl = null): static
    {
        $widget = new static;

        if ($imageUrl) {
            $widget->imageUrl($imageUrl);
        }

        if ($onClickUrl) {
            $widget->onClick($onClickUrl);
        }

        return $widget;
    }

    /**
     * Return a new Image widget instance.
     */
    public static function make(?string $imageUrl = null, ?string $onClickUrl = null): static
    {
        return static::create($imageUrl, $onClickUrl);
    }
}
