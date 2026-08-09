<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Enums\ButtonType;
use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\ButtonList;

class ButtonListTest extends TestCase
{
    public function test_it_formats_button_list_widget()
    {
        $button1 = Button::text('One')->openUrl('https://example.com/1');
        $button2 = Button::text('Two')->openUrl('https://example.com/2');

        $widget = ButtonList::make([$button1, $button2]);

        $this->assertEquals([
            'buttonList' => [
                'buttons' => [
                    [
                        'text' => 'One',
                        'onClick' => [
                            'openLink' => [
                                'url' => 'https://example.com/1',
                            ],
                        ],
                    ],
                    [
                        'text' => 'Two',
                        'onClick' => [
                            'openLink' => [
                                'url' => 'https://example.com/2',
                            ],
                        ],
                    ],
                ],
            ],
        ], $widget->toArray());
    }

    public function test_it_formats_button_styles_and_colour()
    {
        $widget = ButtonList::make([
            Button::text('Outlined')->outlined(),
            Button::text('Filled')->filled(),
            Button::text('Tonal')->filledTonal(),
            Button::text('Borderless')->borderless(),
            Button::text('Custom colour')->type(ButtonType::OUTLINED)->color(1, 0, 0),
        ]);

        $this->assertEquals([
            'buttonList' => [
                'buttons' => [
                    ['text' => 'Outlined', 'type' => 'OUTLINED'],
                    ['text' => 'Filled', 'type' => 'FILLED'],
                    ['text' => 'Tonal', 'type' => 'FILLED_TONAL'],
                    ['text' => 'Borderless', 'type' => 'BORDERLESS'],
                    [
                        'text' => 'Custom colour',
                        'type' => 'OUTLINED',
                        'color' => ['red' => 1.0, 'green' => 0.0, 'blue' => 0.0],
                    ],
                ],
            ],
        ], $widget->toArray());
    }

    public function test_it_rejects_invalid_button_colours()
    {
        $this->expectException(\InvalidArgumentException::class);

        Button::text('Invalid')->color(1.1, 0, 0);
    }

    public function test_it_formats_icon_alternative_text_separately_from_button_alternative_text()
    {
        $button = Button::text('Invite')
            ->icon('INVITE')
            ->iconAltText('Invite a team member')
            ->altText('Opens the invitation form');

        $this->assertEquals([
            'text' => 'Invite',
            'icon' => [
                'knownIcon' => 'INVITE',
                'altText' => 'Invite a team member',
            ],
            'altText' => 'Opens the invitation form',
        ], $button->toArray());
    }
}
