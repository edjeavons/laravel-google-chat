<?php

return [
    /**
     * Notification Delivery Transport Driver.
     *
     * Options: 'webhook' (Incoming Webhook URL) or 'service_account' (Google Chat REST API)
     */
    'driver' => env('GOOGLE_CHAT_DRIVER', 'webhook'),

    /**
     * Default Space Endpoint / Name.
     *
     * Defines the default space where Google Chat messages will be posted.
     */
    'space' => env('GOOGLE_CHAT_DEFAULT_SPACE', null),

    /**
     * Additional Spaces.
     *
     * Named space shortcuts that can be passed to `GoogleChatMessage::to('sales_team')`.
     */
    'spaces' => [
        // 'sales_team' => 'https://chat.googleapis.com/...',
    ],

    /**
     * Service Account Credentials (Required when driver is 'service_account').
     *
     * 'credentials' specifies the path to a JSON key file, a JSON string, or array.
     */
    'service_account' => [
        'credentials' => env('GOOGLE_CHAT_CREDENTIALS'),
    ],

    /**
     * Test Space Webhook Url / Target.
     *
     * When set, all messages will be redirected to this target space regardless of
     * notification or notifiable settings. Useful during development.
     */
    'test_space' => env('GOOGLE_CHAT_TEST_SPACE', null),
];
