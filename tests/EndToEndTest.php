<?php

namespace NotificationChannels\GoogleChat\Tests;

use NotificationChannels\GoogleChat\Tests\Fixtures\TestEndToEndNotification;
use PHPUnit\Framework\TestCase;

class EndToEndTest extends TestCase
{
    /**
     * @var TestEndToEndNotification
     */
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notification = new TestEndToEndNotification;
    }

    public function test_it_generates_complete_correct_array_structure()
    {
        $this->assertEquals(
            [
                'text' => 'This is a test end-to-end notification.',
                'cardsV2' => [
                    // Card 1
                    [
                        'cardId' => 'card-1',
                        'card' => [
                            'header' => [
                                'title' => 'First Card',
                                'subtitle' => 'First Card - Subtitle',
                                'imageUrl' => 'https://picsum.photos/65/65',
                                'imageType' => 'SQUARE',
                            ],
                            'sections' => [
                                // Section 1
                                [
                                    'widgets' => [
                                        // Text Paragraph Widget
                                        [
                                            'textParagraph' => [
                                                'text' => 'Simple paragraph text',
                                            ],
                                        ],
                                    ],
                                ],

                                // Section 2
                                [
                                    'widgets' => [
                                        // Decorated Text Widget
                                        [
                                            'decoratedText' => [
                                                'text' => 'Content',
                                                'topLabel' => 'Top Label',
                                                'bottomLabel' => 'Bottom Label',
                                                'startIcon' => [
                                                    'knownIcon' => 'TRAIN',
                                                ],
                                                'onClick' => [
                                                    'openLink' => [
                                                        'url' => 'https://example.com/key-value-click',
                                                    ],
                                                ],
                                                'button' => [
                                                    'text' => 'Action',
                                                    'onClick' => [
                                                        'openLink' => [
                                                            'url' => 'https://example.com/key-value-button-click',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],

                                        // Image Widget
                                        [
                                            'image' => [
                                                'imageUrl' => 'https://picsum.photos/300/150',
                                                'onClick' => [
                                                    'openLink' => [
                                                        'url' => 'https://example.com/img-clickthrough',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    // Card 2
                    [
                        'cardId' => 'card-2',
                        'card' => [
                            'header' => [
                                'title' => 'Second Card',
                                'subtitle' => 'Second Card - Subtitle',
                                'imageUrl' => 'https://picsum.photos/66/66',
                                'imageType' => 'CIRCLE',
                            ],
                            'sections' => [
                                // Section
                                [
                                    'widgets' => [
                                        // Button List Widget
                                        [
                                            'buttonList' => [
                                                'buttons' => [
                                                    [
                                                        'text' => 'Go There',
                                                        'onClick' => [
                                                            'openLink' => [
                                                                'url' => 'https://example.com/card-2-cta',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $this->notification->toGoogleChat('foo')->toArray()
        );
    }
}
