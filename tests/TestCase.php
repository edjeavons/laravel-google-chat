<?php

namespace NotificationChannels\GoogleChat\Tests;

use NotificationChannels\GoogleChat\GoogleChatServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    public function getPackageProviders($app): array
    {
        return [
            GoogleChatServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('google-chat.test_space', null);
    }
}
