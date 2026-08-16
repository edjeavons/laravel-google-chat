<?php

namespace NotificationChannels\GoogleChat\Tests\Widgets;

use NotificationChannels\GoogleChat\Tests\TestCase;
use NotificationChannels\GoogleChat\Widgets\Divider;

class DividerTest extends TestCase
{
    public function test_it_formats_divider_widget()
    {
        $widget = Divider::make();

        $this->assertEquals([
            'divider' => (object) [],
        ], $widget->toArray());
    }
}
