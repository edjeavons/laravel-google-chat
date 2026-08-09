<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Components\CarouselCard;
use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\ButtonList;
use NotificationChannels\GoogleChat\Widgets\Carousel;
use NotificationChannels\GoogleChat\Widgets\Image;
use NotificationChannels\GoogleChat\Widgets\TextParagraph;

class CarouselTest extends TestCase
{
    public function test_it_formats_carousel_cards_with_footer_widgets(): void
    {
        $carousel = Carousel::make([
            CarouselCard::make([
                Image::make('https://example.com/helpdesk.png')->altText('Helpdesk'),
                TextParagraph::make('<b>Helpdesk</b>'),
            ])->footerWidgets(
                ButtonList::make(Button::text('Raise a ticket')->openUrl('https://example.com/tickets')),
            ),
            CarouselCard::make(TextParagraph::make('<b>Guide</b>')),
        ]);

        $this->assertEquals([
            'carousel' => [
                'carouselCards' => [
                    [
                        'widgets' => [
                            [
                                'image' => [
                                    'imageUrl' => 'https://example.com/helpdesk.png',
                                    'altText' => 'Helpdesk',
                                ],
                            ],
                            [
                                'textParagraph' => ['text' => '<b>Helpdesk</b>'],
                            ],
                        ],
                        'footerWidgets' => [
                            [
                                'buttonList' => [
                                    'buttons' => [
                                        [
                                            'text' => 'Raise a ticket',
                                            'onClick' => [
                                                'openLink' => ['url' => 'https://example.com/tickets'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'widgets' => [
                            [
                                'textParagraph' => ['text' => '<b>Guide</b>'],
                            ],
                        ],
                    ],
                ],
            ],
        ], $carousel->toArray());
    }
}
