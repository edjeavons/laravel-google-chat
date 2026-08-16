<?php

namespace NotificationChannels\GoogleChat;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Concerns\ValidatesCardComponents;

class Card implements Arrayable
{
    use ValidatesCardComponents;

    /**
     * The card identifier.
     */
    protected ?string $cardId = null;

    /**
     * The card payload.
     */
    protected array $payload = [
        'sections' => [],
    ];

    /**
     * Set a custom card identifier.
     */
    public function id(string $id): static
    {
        $this->cardId = $id;

        return $this;
    }

    /**
     * Set a custom card identifier.
     */
    public function cardId(string $id): static
    {
        return $this->id($id);
    }

    /**
     * Get the card identifier.
     */
    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    /**
     * Configure the header content of the card.
     *
     * @param  string  $title  The title of the card
     * @param  string|null  $subtitle  Secondary text displayed below the title
     * @param  string|null  $imageUrl  Display an image/avatar for the card header
     * @param  Enums\ImageType|string|null  $imageType  Image shape (SQUARE or CIRCLE)
     * @param  string|null  $altText  Alternative text for accessibility
     */
    public function header(string $title, ?string $subtitle = null, ?string $imageUrl = null, Enums\ImageType|string|null $imageType = null, ?string $altText = null): static
    {
        $header = [
            'title' => $title,
        ];

        if ($subtitle) {
            $header['subtitle'] = $subtitle;
        }

        if ($imageUrl) {
            $header['imageUrl'] = $imageUrl;
        }

        if ($imageType) {
            $header['imageType'] = $imageType instanceof Enums\ImageType ? $imageType->value : $imageType;
        }

        if ($altText) {
            $header['altText'] = $altText;
        }

        $this->payload['header'] = $header;

        return $this;
    }

    /**
     * Add one or more sections to the card.
     *
     * @param  Section|Section[]|\Closure  $section
     */
    public function section(mixed $section): static
    {
        if ($section instanceof \Closure) {
            $sec = Section::make();
            $section($sec);
            $sections = [$sec];
        } else {
            $sections = Arr::wrap($section);
        }

        $this->guardOnlyInstancesOf(Section::class, $sections);

        $this->payload['sections'] = array_merge($this->payload['sections'] ?? [], $sections);

        return $this;
    }

    /**
     * Add card action items to the card's overflow menu.
     *
     * @param  Components\CardAction|Components\CardAction[]  $cardActions
     */
    public function cardActions(mixed $cardActions): static
    {
        $actions = Arr::wrap($cardActions);

        $this->guardOnlyInstancesOf(Components\CardAction::class, $actions);

        foreach ($actions as $action) {
            $this->payload['cardActions'][] = $action->toArray();
        }

        return $this;
    }

    /**
     * Serialize the card to an array representation.
     */
    public function toArray(): array
    {
        $payload = $this->payload;

        if (! empty($payload['sections'])) {
            $payload['sections'] = array_map(function ($section) {
                return $section instanceof Arrayable ? $section->toArray() : $section;
            }, $payload['sections']);
        }

        return $payload;
    }

    /**
     * Return a new Google Chat Card instance.
     *
     * @param  Section|Section[]|null  $section
     */
    public static function create($section = null): static
    {
        $card = new static;

        if ($section) {
            $card->section($section);
        }

        return $card;
    }

    /**
     * Return a new Google Chat Card instance.
     */
    public static function make($section = null): static
    {
        return static::create($section);
    }
}
