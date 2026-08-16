<?php

namespace NotificationChannels\GoogleChat\Tests\Fixtures;

use Illuminate\Notifications\Notification;
use NotificationChannels\GoogleChat\Card;
use NotificationChannels\GoogleChat\Components\Button;
use NotificationChannels\GoogleChat\Enums\Icon;
use NotificationChannels\GoogleChat\Enums\ImageType;
use NotificationChannels\GoogleChat\GoogleChatChannel;
use NotificationChannels\GoogleChat\GoogleChatMessage;
use NotificationChannels\GoogleChat\Section;
use NotificationChannels\GoogleChat\Widgets\ButtonList;
use NotificationChannels\GoogleChat\Widgets\DecoratedText;
use NotificationChannels\GoogleChat\Widgets\Image;
use NotificationChannels\GoogleChat\Widgets\TextParagraph;

class TestEndToEndNotification extends Notification
{
    public function via($notifiable)
    {
        return [GoogleChatChannel::class];
    }

    public function toGoogleChat($notifiable)
    {
        $message = GoogleChatMessage::create()
            ->text('This is a test end-to-end notification.')
            ->card([
                Card::create([
                    Section::create(
                        TextParagraph::create('Simple paragraph text')
                    ),
                    Section::create()
                        ->widget(
                            DecoratedText::create('Content')
                                ->topLabel('Top Label')
                                ->bottomLabel('Bottom Label')
                                ->startIcon(Icon::TRAIN)
                                ->openUrl('https://example.com/key-value-click')
                                ->button(
                                    Button::text('Action')->openUrl('https://example.com/key-value-button-click')
                                )
                        )
                        ->widget(
                            Image::create('https://picsum.photos/300/150', 'https://example.com/img-clickthrough')
                        ),
                ])->header(
                    'First Card',
                    'First Card - Subtitle',
                    'https://picsum.photos/65/65',
                    ImageType::SQUARE
                ),
                Card::create(
                    Section::create(
                        ButtonList::create(
                            Button::text('Go There')->openUrl('https://example.com/card-2-cta')
                        )
                    )
                )->header(
                    'Second Card',
                    'Second Card - Subtitle',
                    'https://picsum.photos/66/66',
                    ImageType::CIRCLE
                ),
            ]);

        return $message;
    }
}
