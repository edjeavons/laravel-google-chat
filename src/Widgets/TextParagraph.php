<?php

namespace NotificationChannels\GoogleChat\Widgets;

class TextParagraph extends AbstractWidget
{
    /**
     * Append text content to the widget.
     */
    public function text(string $message): TextParagraph
    {
        $this->payload['text'] = ($this->payload['text'] ?? '').$message;

        return $this;
    }

    /**
     * Limit the number of displayed lines.
     */
    public function maxLines(int $maxLines): static
    {
        $this->payload['maxLines'] = $maxLines;

        return $this;
    }

    /**
     * Append bold text content.
     */
    public function bold(string $message): TextParagraph
    {
        return $this->text("<b>{$message}</b>");
    }

    /**
     * Append italic text content.
     */
    public function italic(string $message): TextParagraph
    {
        return $this->text("<i>{$message}</i>");
    }

    /**
     * Append underline text content.
     */
    public function underline(string $message): TextParagraph
    {
        return $this->text("<u>{$message}</u>");
    }

    /**
     * Append strikethrough text content.
     */
    public function strikethrough(string $message): TextParagraph
    {
        return $this->text("<strike>{$message}</strike>");
    }

    /**
     * Append strikethrough text content.
     */
    public function strike(string $message): TextParagraph
    {
        return $this->strikethrough($message);
    }

    /**
     * Append colored text content.
     */
    public function color(string $message, string $hex): TextParagraph
    {
        return $this->text("<font color=\"{$hex}\">{$message}</font>");
    }

    /**
     * Append a text link.
     */
    public function link(string $link, ?string $displayText = null): TextParagraph
    {
        return $this->text("<a href=\"{$link}\">".($displayText ?? $link).'</a>');
    }

    /**
     * Append a line break.
     */
    public function break(): TextParagraph
    {
        return $this->text('<br>');
    }

    /**
     * Return a new Text Paragraph widget instance.
     */
    public static function create(?string $message = null): static
    {
        $widget = new static;

        if ($message) {
            $widget->text($message);
        }

        return $widget;
    }

    /**
     * Return a new Text Paragraph widget instance.
     */
    public static function make(?string $message = null): static
    {
        return static::create($message);
    }
}
