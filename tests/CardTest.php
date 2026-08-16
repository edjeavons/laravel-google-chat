<?php

namespace NotificationChannels\GoogleChat\Tests;

use NotificationChannels\GoogleChat\Card;
use NotificationChannels\GoogleChat\Enums\ImageType;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\Section;
use stdClass;

class CardTest extends TestCase
{
    public function test_it_creates_header_element()
    {
        $card = Card::create()
            ->header(
                'Header Title',
                'Header Subtitle',
                'Header Image URL',
                ImageType::CIRCLE,
                'Avatar'
            );

        $this->assertEquals(
            [
                'header' => [
                    'title' => 'Header Title',
                    'subtitle' => 'Header Subtitle',
                    'imageUrl' => 'Header Image URL',
                    'imageType' => 'CIRCLE',
                    'altText' => 'Avatar',
                ],
                'sections' => [],
            ],
            $card->toArray()
        );
    }

    public function test_it_supports_card_id()
    {
        $card = Card::make()->id('custom-card-id');

        $this->assertEquals('custom-card-id', $card->getCardId());
    }

    public function test_it_rejects_non_sections()
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Cannot pass object of type: stdClass');

        Card::create(new stdClass);
    }

    public function test_it_can_add_sections_and_closures()
    {
        $sectionA = Section::create()->header('Section A');

        $card = Card::create($sectionA)
            ->section(fn (Section $s) => $s->header('Section B'));

        $this->assertEquals(
            [
                'sections' => [
                    ['header' => 'Section A', 'widgets' => []],
                    ['header' => 'Section B', 'widgets' => []],
                ],
            ],
            $card->toArray()
        );
    }
}
