<?php

namespace NotificationChannels\GoogleChat;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Concerns\ValidatesCardComponents;
use NotificationChannels\GoogleChat\Widgets\AbstractWidget;

class Section implements Arrayable
{
    use ValidatesCardComponents;

    /**
     * The section payload.
     *
     * @var array
     */
    protected $payload = [
        'widgets' => [],
    ];

    /**
     * Set the section header text.
     */
    public function header(string $text): Section
    {
        $this->payload['header'] = $text;

        return $this;
    }

    /**
     * Add one or more widgets to this section.
     *
     * @param  AbstractWidget|AbstractWidget[]  $widget
     */
    public function widget($widget): Section
    {
        $widgets = Arr::wrap($widget);

        $this->guardOnlyInstancesOf(AbstractWidget::class, $widgets);

        $this->payload['widgets'] = array_merge($this->payload['widgets'], $widgets);

        return $this;
    }

    /**
     * Set whether the section is collapsible.
     */
    public function collapsible(bool $collapsible = true, int $uncollapsibleWidgetsCount = 1): static
    {
        $this->payload['collapsible'] = $collapsible;
        $this->payload['uncollapsibleWidgetsCount'] = $uncollapsibleWidgetsCount;

        return $this;
    }

    /**
     * Configure the buttons displayed when the section is expanded or collapsed.
     */
    public function collapseControl(
        Components\Button $collapseButton,
        Components\Button $expandButton,
        Enums\HorizontalAlignment|string|null $horizontalAlignment = null,
    ): static {
        $collapseControl = [
            'collapseButton' => $collapseButton->toArray(),
            'expandButton' => $expandButton->toArray(),
        ];

        if ($horizontalAlignment !== null) {
            $collapseControl['horizontalAlignment'] = $horizontalAlignment instanceof Enums\HorizontalAlignment
                ? $horizontalAlignment->value
                : $horizontalAlignment;
        }

        $this->payload['collapseControl'] = $collapseControl;

        return $this;
    }

    /**
     * Add a DecoratedText widget.
     */
    public function decoratedText(Widgets\DecoratedText|string $text, ?string $topLabel = null, Enums\Icon|Components\MaterialIcon|string|null $startIcon = null): static
    {
        if ($text instanceof Widgets\DecoratedText) {
            return $this->widget($text);
        }

        $widget = Widgets\DecoratedText::make($text);

        if ($topLabel) {
            $widget->topLabel($topLabel);
        }

        if ($startIcon) {
            $widget->startIcon($startIcon);
        }

        return $this->widget($widget);
    }

    /**
     * Add a Divider widget.
     */
    public function divider(): static
    {
        return $this->widget(Widgets\Divider::make());
    }

    /**
     * Add a ButtonList widget.
     */
    public function buttonList(Components\Button|array $buttons): static
    {
        return $this->widget(Widgets\ButtonList::make($buttons));
    }

    /**
     * Add a ChipList widget.
     */
    public function chipList(Components\Chip|array $chips): static
    {
        $this->widget(Widgets\ChipList::make($chips));

        return $this;
    }

    /**
     * Add a Columns widget.
     */
    public function columns(array|\Closure $widgets): static
    {
        $col = Widgets\Columns::make();
        $col->column($widgets);

        return $this->widget($col);
    }

    /**
     * Add a TextParagraph widget.
     */
    public function textParagraph(?string $text = null): static
    {
        return $this->widget(Widgets\TextParagraph::make($text));
    }

    /**
     * Add an Image widget.
     */
    public function image(?string $imageUrl = null, ?string $onClickUrl = null): static
    {
        return $this->widget(Widgets\Image::make($imageUrl, $onClickUrl));
    }

    /**
     * Add a Grid widget.
     */
    public function grid(Widgets\Grid $grid): Section
    {
        return $this->widget($grid);
    }

    /**
     * Serialize the section to an array representation.
     */
    public function toArray(): array
    {
        $payload = $this->payload;

        if (! empty($payload['widgets'])) {
            $payload['widgets'] = array_map(function ($widget) {
                return $widget instanceof Arrayable ? $widget->toArray() : $widget;
            }, $payload['widgets']);
        }

        return $payload;
    }

    /**
     * Return a new Google Chat Section instance.
     *
     * @param  AbstractWidget|AbstractWidget[]  $widgets
     */
    public static function create($widgets = null): static
    {
        $section = new static;

        if ($widgets) {
            $section->widget($widgets);
        }

        return $section;
    }

    /**
     * Return a new Google Chat Section instance.
     */
    public static function make($widgets = null): static
    {
        return static::create($widgets);
    }
}
