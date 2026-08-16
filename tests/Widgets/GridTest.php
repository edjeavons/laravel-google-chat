<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Components\GridItem;
use NotificationChannels\GoogleChat\Enums\BorderType;
use NotificationChannels\GoogleChat\Enums\ImageCropStyle;
use NotificationChannels\GoogleChat\Enums\TextAlignment;
use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\Grid;

class GridTest extends TestCase
{
    public function test_it_formats_a_grid_with_items_and_an_action(): void
    {
        $grid = Grid::make([
            GridItem::make()
                ->image('https://example.com/item-1.png')
                ->cropStyle(ImageCropStyle::SQUARE)
                ->borderStyle(BorderType::STROKE)
                ->title('Item 1')
                ->subtitle('First item')
                ->textAlignment(TextAlignment::CENTER),
            GridItem::make()
                ->image('https://example.com/item-2.png')
                ->title('Item 2')
                ->textAlignment(TextAlignment::CENTER),
        ])
            ->title('Collection')
            ->columnCount(2)
            ->openUrl('https://example.com/items');

        $this->assertEquals([
            'grid' => [
                'items' => [
                    [
                        'image' => [
                            'imageUri' => 'https://example.com/item-1.png',
                            'cropStyle' => ['type' => 'SQUARE'],
                            'borderStyle' => ['type' => 'STROKE'],
                        ],
                        'title' => 'Item 1',
                        'subtitle' => 'First item',
                        'textAlignment' => 'CENTER',
                    ],
                    [
                        'image' => [
                            'imageUri' => 'https://example.com/item-2.png',
                        ],
                        'title' => 'Item 2',
                        'textAlignment' => 'CENTER',
                    ],
                ],
                'title' => 'Collection',
                'columnCount' => 2,
                'onClick' => [
                    'openLink' => ['url' => 'https://example.com/items'],
                ],
            ],
        ], $grid->toArray());
    }
}
