<?php

namespace NotificationChannels\GoogleChat\Tests;

use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Components\Chip;
use NotificationChannels\GoogleChat\Enums\HorizontalAlignment;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\Section;
use NotificationChannels\GoogleChat\Widgets\TextParagraph;
use stdClass;

class SectionTest extends TestCase
{
    public function test_it_can_set_header_text()
    {
        $section = Section::create()->header('Example Header');

        $this->assertEquals(
            [
                'header' => 'Example Header',
                'widgets' => [],
            ],
            $section->toArray()
        );
    }

    public function test_it_rejects_non_widgets()
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Cannot pass object of type: stdClass');

        Section::create(new stdClass);
    }

    public function test_it_can_add_widgets()
    {
        $widget = TextParagraph::create('Text content');

        $section = Section::create($widget);

        $this->assertEquals(
            [
                'widgets' => [
                    [
                        'textParagraph' => [
                            'text' => 'Text content',
                        ],
                    ],
                ],
            ],
            $section->toArray()
        );
    }

    public function test_it_can_add_chip_lists(): void
    {
        $section = Section::create()->chipList(Chip::label('New'));

        $this->assertEquals([
            'widgets' => [
                [
                    'chipList' => [
                        'chips' => [
                            ['label' => 'New'],
                        ],
                    ],
                ],
            ],
        ], $section->toArray());
    }

    public function test_it_can_configure_a_collapse_control(): void
    {
        $section = Section::create()
            ->collapsible()
            ->collapseControl(
                Button::text('Hide')->borderless(),
                Button::text('Show')->borderless(),
                HorizontalAlignment::CENTER,
            );

        $this->assertEquals([
            'widgets' => [],
            'collapsible' => true,
            'uncollapsibleWidgetsCount' => 1,
            'collapseControl' => [
                'collapseButton' => ['text' => 'Hide', 'type' => 'BORDERLESS'],
                'expandButton' => ['text' => 'Show', 'type' => 'BORDERLESS'],
                'horizontalAlignment' => 'CENTER',
            ],
        ], $section->toArray());
    }
}
