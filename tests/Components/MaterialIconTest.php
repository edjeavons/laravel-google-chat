<?php

namespace NotificationChannels\GoogleChat\Tests\Components;

use NotificationChannels\GoogleChat\Components\MaterialIcon;
use NotificationChannels\GoogleChat\Tests\TestCase;

class MaterialIconTest extends TestCase
{
    public function test_it_formats_a_material_icon(): void
    {
        $icon = MaterialIcon::make('settings')
            ->fill()
            ->weight(500)
            ->grade(200);

        $this->assertEquals([
            'materialIcon' => [
                'name' => 'settings',
                'fill' => true,
                'weight' => 500,
                'grade' => 200,
            ],
        ], $icon->toArray());
    }

    public function test_it_rejects_invalid_weight(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        MaterialIcon::make('settings')->weight(350);
    }

    public function test_it_rejects_invalid_grade(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        MaterialIcon::make('settings')->grade(100);
    }
}
