<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Enums\HorizontalAlignment;
use NotificationChannels\GoogleChat\Enums\HorizontalSizeStyle;
use NotificationChannels\GoogleChat\Enums\VerticalAlignment;
use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\ButtonList;
use NotificationChannels\GoogleChat\Widgets\Columns;

class ColumnsTest extends TestCase
{
    public function test_it_configures_a_full_width_right_aligned_column(): void
    {
        $columns = Columns::make()->column(
            [ButtonList::make(Button::text('Continue'))],
            HorizontalSizeStyle::FILL_AVAILABLE_SPACE,
            HorizontalAlignment::END,
            VerticalAlignment::CENTER,
        );

        $this->assertEquals([
            'columns' => [
                'columnItems' => [
                    [
                        'widgets' => [
                            [
                                'buttonList' => [
                                    'buttons' => [
                                        ['text' => 'Continue'],
                                    ],
                                ],
                            ],
                        ],
                        'horizontalSizeStyle' => 'FILL_AVAILABLE_SPACE',
                        'horizontalAlignment' => 'END',
                        'verticalAlignment' => 'CENTER',
                    ],
                ],
            ],
        ], $columns->toArray());
    }
}
