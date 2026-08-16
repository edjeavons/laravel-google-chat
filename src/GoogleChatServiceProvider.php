<?php

namespace NotificationChannels\GoogleChat;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class GoogleChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            realpath(__DIR__.'/../config/google-chat.php') => config_path('google-chat.php'),
        ], 'google-chat-config');

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('googleChat', function ($app) {
                return $app->make(GoogleChatChannel::class);
            });
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/google-chat.php'), 'google-chat');
    }
}
