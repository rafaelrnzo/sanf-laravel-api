<?php

return [
    'headers' => [
        'version_number' => 'X-App-Version-Number',
        'version_string' => 'X-App-Version-String',
    ],
    'android' => [
        'minimum_version_number' => env('ANDROID_MIN_VER_NUM', 0),
    ],
    'ios' => [
        'minimum_version_number' => env('IOS_MIN_VER_NUM', 0),
    ],
];
