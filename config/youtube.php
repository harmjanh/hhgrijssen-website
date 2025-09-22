<?php

/*
|--------------------------------------------------------------------------
| YouTube Data API v3 Configuration
|--------------------------------------------------------------------------
|
| Configuration for the official Google API Client Library for YouTube
| API key can be acquired from: https://console.developers.google.com
*/

return [
    'api_key' => env('YOUTUBE_API_KEY', 'YOUR_API_KEY'),
    'channel_id' => env('YOUTUBE_CHANNEL_ID', null),
    'application_name' => env('YOUTUBE_APPLICATION_NAME', 'Church Website'),
    'yt_dlp_path' => env('YT_DLP_PATH', '/opt/homebrew/bin/yt-dlp'),

    // OAuth 2.0 Configuration for private video access
    'oauth' => [
        'client_id' => env('YOUTUBE_OAUTH_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_OAUTH_CLIENT_SECRET'),
        'redirect_uri' => env('YOUTUBE_OAUTH_REDIRECT_URI', 'http://localhost:8080/oauth2callback'),
        'scopes' => [
            'https://www.googleapis.com/auth/youtube.readonly',
            'https://www.googleapis.com/auth/youtube.force-ssl'
        ],
        'access_type' => 'offline',
        'approval_prompt' => 'force',
    ],

    // Token storage configuration
    'token_storage' => [
        'type' => 'file', // 'file' or 'database'
        'path' => storage_path('app/youtube-tokens.json'),
    ],
];
