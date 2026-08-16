<?php

namespace NotificationChannels\GoogleChat\Tests;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\GoogleChatChannel;
use NotificationChannels\GoogleChat\Tests\Fixtures\TestNotifiable;
use NotificationChannels\GoogleChat\Tests\Fixtures\TestNotification;

class GoogleChatChannelTest extends TestCase
{
    public function test_it_rejects_sending_when_to_google_chat_method_undefined()
    {
        $notification = $this->createMock(Notification::class);

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Notification of class: '.get_class($notification).' must define a `toGoogleChat()` method in order to send via the Google Chat Channel');

        $this->newChannel()->send('foo', $notification);
    }

    public function test_it_rejects_sending_when_non_google_chat_message_supplied()
    {
        $notification = $this->createMock(TestNotification::class);
        $notification->expects($this->once())
            ->method('toGoogleChat')
            ->with('notifiable')
            ->willReturn('This value is invalid, as it is not an instance of Google Chat Message');

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage("Expected a message instance of type NotificationChannels\GoogleChat\GoogleChatMessage - Actually received string");

        $this->newChannel()->send('notifiable', $notification);
    }

    public function test_it_rejects_sending_when_no_space_configured()
    {
        $notification = $this->newNotification();

        $notifiable = new class
        {
            use Notifiable;
        };

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('No webhook URL was available when sending the Google Chat notification');

        $this->newChannel()->send($notifiable, $notification);
    }

    public function test_it_sends_to_default_space()
    {
        Http::fake([
            'https://chat.googleapis.com/default-space*' => Http::response(['name' => 'spaces/AAA/messages/BBB'], 200),
        ]);

        config(['google-chat.space' => 'https://chat.googleapis.com/default-space']);

        $notifiable = new class
        {
            use Notifiable;
        };

        $notification = $this->newNotification();

        $this->newChannel()->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://chat.googleapis.com/default-space');
        });
    }

    public function test_it_sends_to_notifiable_space()
    {
        Http::fake([
            'https://chat.googleapis.com/notifiable-space*' => Http::response(['name' => 'spaces/AAA/messages/BBB'], 200),
        ]);

        config(['google-chat.space' => 'https://chat.googleapis.com/default-space']);

        $notifiable = $this->newNotifiable('https://chat.googleapis.com/notifiable-space');
        $notification = $this->newNotification();

        $this->newChannel()->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://chat.googleapis.com/notifiable-space');
        });
    }

    public function test_it_resolves_space_config_nested_key()
    {
        Http::fake([
            'https://chat.googleapis.com/alternate-space*' => Http::response([], 200),
        ]);

        config([
            'google-chat.spaces.test' => 'https://chat.googleapis.com/alternate-space',
        ]);

        $notifiable = $this->newNotifiable('test');
        $notification = $this->newNotification();

        $this->newChannel()->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://chat.googleapis.com/alternate-space');
        });
    }

    public function test_it_overrides_destination_when_test_space_configured()
    {
        Http::fake([
            'https://chat.googleapis.com/override-test-space*' => Http::response([], 200),
        ]);

        config([
            'google-chat.space' => 'https://chat.googleapis.com/default-space',
            'google-chat.test_space' => 'https://chat.googleapis.com/override-test-space',
        ]);

        $notifiable = $this->newNotifiable('https://chat.googleapis.com/notifiable-space');
        $notification = $this->newNotification();

        $this->newChannel()->send($notifiable, $notification);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'https://chat.googleapis.com/override-test-space');
        });
    }

    public function test_it_handles_http_errors()
    {
        Http::fake([
            'https://chat.googleapis.com/error-space*' => Http::response(['error' => 'Invalid payload'], 400),
        ]);

        config(['google-chat.space' => 'https://chat.googleapis.com/error-space']);

        $notifiable = new class
        {
            use Notifiable;
        };

        $notification = $this->newNotification();

        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Failed to send Google Chat message, encountered HTTP status 400');

        $this->newChannel()->send($notifiable, $notification);
    }

    private function newChannel(): GoogleChatChannel
    {
        return new GoogleChatChannel;
    }

    private function newNotification(): TestNotification
    {
        return new TestNotification;
    }

    private function newNotifiable(?string $space = null): TestNotifiable
    {
        return new TestNotifiable($space);
    }
}
