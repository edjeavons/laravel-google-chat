<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Components\MaterialIcon;
use NotificationChannels\GoogleChat\Enums\Icon;
use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\DecoratedText;

class DecoratedTextTest extends TestCase
{
    public function test_it_formats_decorated_text_widget()
    {
        $widget = DecoratedText::make('Primary text')
            ->topLabel('Top label')
            ->bottomLabel('Bottom label')
            ->startIcon(Icon::STAR)
            ->endIcon(Icon::EMAIL)
            ->openUrl('https://example.com');

        $this->assertEquals([
            'decoratedText' => [
                'text' => 'Primary text',
                'topLabel' => 'Top label',
                'bottomLabel' => 'Bottom label',
                'startIcon' => [
                    'knownIcon' => 'STAR',
                ],
                'endIcon' => [
                    'knownIcon' => 'EMAIL',
                ],
                'onClick' => [
                    'openLink' => [
                        'url' => 'https://example.com',
                    ],
                ],
            ],
        ], $widget->toArray());
    }

    public function test_it_formats_decorated_text_with_button()
    {
        $button = Button::text('Click me')->openUrl('https://example.com/btn');
        $widget = DecoratedText::make('Primary text')->button($button);

        $this->assertEquals([
            'decoratedText' => [
                'text' => 'Primary text',
                'button' => [
                    'text' => 'Click me',
                    'onClick' => [
                        'openLink' => [
                            'url' => 'https://example.com/btn',
                        ],
                    ],
                ],
            ],
        ], $widget->toArray());
    }

    public function test_it_formats_material_icons(): void
    {
        $widget = DecoratedText::make('Settings')
            ->startIcon(MaterialIcon::make('settings'))
            ->endIcon(MaterialIcon::make('arrow_forward')->weight(700));

        $this->assertEquals([
            'decoratedText' => [
                'text' => 'Settings',
                'startIcon' => [
                    'materialIcon' => ['name' => 'settings'],
                ],
                'endIcon' => [
                    'materialIcon' => [
                        'name' => 'arrow_forward',
                        'weight' => 700,
                    ],
                ],
            ],
        ], $widget->toArray());
    }
}
