<?php

return [
'rest_countries' => [
    'key' => env('REST_COUNTRIES_API_KEY'),
],

'gnews' => [
    'key' => env('GNEWS_API_KEY'),
    'language' => env('GNEWS_LANGUAGE', 'en'),
    'batch_size' => (int) env('GNEWS_BATCH_SIZE', 4),
    'max_articles' => (int) env('GNEWS_MAX_ARTICLES', 10),
],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

'translation' => [
    'enabled' => env('TRANSLATION_ENABLED', false),

    'provider' => env(
        'TRANSLATION_PROVIDER',
        'libretranslate'
    ),

    'timeout' => (int) env(
        'TRANSLATION_TIMEOUT',
        30
    ),

    'libretranslate' => [
        'url' => env(
            'LIBRETRANSLATE_URL',
            'https://libretranslate.com'
        ),

        'key' => env(
            'LIBRETRANSLATE_API_KEY'
        ),

        // Bahasa yang tersedia pada instance publik LibreTranslate.
        // Fallback ketika endpoint /languages sedang tidak tersedia.
        'supported_locales' => ['en', 'id', 'ja', 'ar', 'zh-Hans'],
    ],

    'google' => [
        'url' => env(
            'GOOGLE_TRANSLATE_URL',
            'https://translation.googleapis.com/language/translate/v2'
        ),

        'key' => env(
            'GOOGLE_TRANSLATE_API_KEY'
        ),
    ],
],

];
