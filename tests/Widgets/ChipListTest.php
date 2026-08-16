<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Components\Chip;
use NotificationChannels\GoogleChat\Components\MaterialIcon;
use NotificationChannels\GoogleChat\Enums\ChipListLayout;
use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\ChipList;

class ChipListTest extends TestCase
{
    public function test_it_formats_a_chip_list(): void
    {
        $chips = ChipList::make([
            Chip::label('Chip')->openUrl('https://example.com'),
            Chip::label('Chip with Icon')
                ->icon(MaterialIcon::make('alarm'))
                ->openUrl('https://example.com'),
            Chip::label('Disabled Chip')->disabled()->openUrl('https://example.com'),
        ])->horizontalScrollable();

        $this->assertEquals([
            'chipList' => [
                'chips' => [
                    [
                        'label' => 'Chip',
                        'onClick' => [
                            'openLink' => ['url' => 'https://example.com'],
                        ],
                    ],
                    [
                        'label' => 'Chip with Icon',
                        'icon' => [
                            'materialIcon' => ['name' => 'alarm'],
                        ],
                        'onClick' => [
                            'openLink' => ['url' => 'https://example.com'],
                        ],
                    ],
                    [
                        'label' => 'Disabled Chip',
                        'disabled' => true,
                        'onClick' => [
                            'openLink' => ['url' => 'https://example.com'],
                        ],
                    ],
                ],
                'layout' => 'HORIZONTAL_SCROLLABLE',
            ],
        ], $chips->toArray());
    }

    public function test_it_accepts_a_layout_enum(): void
    {
        $chips = ChipList::make()->layout(ChipListLayout::WRAPPED);

        $this->assertEquals([
            'chipList' => [
                'layout' => 'WRAPPED',
            ],
        ], $chips->toArray());
    }
}
