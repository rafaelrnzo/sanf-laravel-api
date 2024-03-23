<?php

return [
    // https://console.firebase.google.com/u/0/project/_/settings/general
    'web_config' => [
        'api_key' => env('FIREBASE_CONFIG_API_KEY'),
        'project_id' => env('FIREBASE_CONFIG_PROJECT_ID'),
        'messaging_sender_id' => env('FIREBASE_CONFIG_MESSAGING_SENDER_ID'),
    ],

    // https://console.firebase.google.com/u/0/project/_/settings/cloudmessaging
    'web_push_key' => env('FIREBASE_WEB_PUSH_KEY'),

    // Firebase Service Account (json file)
    'key' => __DIR__ . DIRECTORY_SEPARATOR . 'firebase-keys' . DIRECTORY_SEPARATOR . env('FIREBASE_SA_FILE', 'firebase.json'),
];
