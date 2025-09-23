<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Telegram Bot Token.
    | To get a token, start a chat with @BotFather on Telegram.
    |
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Chat ID for notifications
    |--------------------------------------------------------------------------
    |
    | Default chat ID where notifications will be sent if user doesn't have one.
    | Can be a group chat ID or channel ID.
    |
    */
    'default_chat_id' => env('TELEGRAM_DEFAULT_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    |
    | URL where Telegram will send updates for your bot.
    | Leave empty if you're not using webhooks.
    |
    */
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Bot Username
    |--------------------------------------------------------------------------
    |
    | Your bot's username (without @).
    |
    */
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure when notifications should be sent.
    |
    */
    'notifications' => [
        'enabled' => env('TELEGRAM_NOTIFICATIONS_ENABLED', true),
        'time' => env('TELEGRAM_NOTIFICATION_TIME', '08:00'), // Format: HH:MM
        'timezone' => env('TELEGRAM_TIMEZONE', 'America/Mexico_City'),
    ],
];
