<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\TextParagraph;

class TextParagraphTest extends TestCase
{
    public function test_it_can_create_with_simple_text()
    {
        $widget = TextParagraph::create('Example Text');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => 'Example Text',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_appends_text()
    {
        $widget = TextParagraph::create('Text 1')
            ->text('-Text 2')
            ->text('-Text 3');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => 'Text 1-Text 2-Text 3',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_can_limit_displayed_lines(): void
    {
        $widget = TextParagraph::create('Example Text')->maxLines(2);

        $this->assertEquals([
            'textParagraph' => [
                'text' => 'Example Text',
                'maxLines' => 2,
            ],
        ], $widget->toArray());
    }

    public function test_it_creates_bold_text()
    {
        $widget = TextParagraph::create()->bold('Bold Text');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<b>Bold Text</b>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_creates_italic_text()
    {
        $widget = TextParagraph::create()->italic('Italic Text');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<i>Italic Text</i>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_creates_underline_text()
    {
        $widget = TextParagraph::create()->underline('Underline Text');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<u>Underline Text</u>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_creates_strikethrough_text()
    {
        $widget = TextParagraph::create()->strikethrough('Strikethrough Text');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<strike>Strikethrough Text</strike>',
                ],
            ],
            $widget->toArray()
        );

        $widget = TextParagraph::create()->strike('Strikethrough Text');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<strike>Strikethrough Text</strike>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_creates_colored_text()
    {
        $widget = TextParagraph::create()->color('Colored Text', '#0000FF');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<font color="#0000FF">Colored Text</font>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_creates_link_text()
    {
        $widget = TextParagraph::create()->link('https://example.com');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<a href="https://example.com">https://example.com</a>',
                ],
            ],
            $widget->toArray()
        );

        $widget = TextParagraph::create()->link('https://example.com', 'Example');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<a href="https://example.com">Example</a>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_creates_break()
    {
        $widget = TextParagraph::create()->break();

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<br>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_appends_converted_markdown()
    {
        $widget = TextParagraph::create()->markdown('* **Feature:** Add new item');

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<ul><li><b>Feature:</b> Add new item</li></ul>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_converts_full_markdown_to_card_html()
    {
        $markdown = <<<'MARKDOWN'
## Summary

This update improves reliability.

## Updates

- **Add:** New feature
- **Fix:** Bug fix
MARKDOWN;

        $widget = TextParagraph::create()->markdown($markdown);

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<b>Summary</b><br><br>This update improves reliability.<br><br><b>Updates</b><br><br><ul><li><b>Add:</b> New feature</li><li><b>Fix:</b> Bug fix</li></ul>',
                ],
            ],
            $widget->toArray()
        );
    }

    public function test_it_preserves_user_provided_html()
    {
        $markdown = <<<'MARKDOWN'
## Alert <font color="#ff0000">Critical</font>

<u>Underlined</u> text with <a href="https://example.com">custom link</a>.
MARKDOWN;

        $widget = TextParagraph::create()->markdown($markdown);

        $this->assertEquals(
            [
                'textParagraph' => [
                    'text' => '<b>Alert <font color="#ff0000">Critical</font></b><br><br><u>Underlined</u> text with <a href="https://example.com">custom link</a>.',
                ],
            ],
            $widget->toArray()
        );
    }
}
