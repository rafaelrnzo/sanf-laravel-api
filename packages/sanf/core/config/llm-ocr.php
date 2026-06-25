<?php

return [
    'api_key' => env('LLM_OCR_API_KEY', ''),
    'max_file_size' => env('LLM_OCR_MAX_FILE_SIZE', 10240),

    'gemini' => [
        'api_key' => env('LLM_OCR_GEMINI_API_KEY', ''),
        'model' => env('LLM_OCR_GEMINI_MODEL', 'gemini-2.5-flash'),
        'base_url' => env('LLM_OCR_GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'connect_timeout' => env('LLM_OCR_GEMINI_CONNECT_TIMEOUT', 15),
        'timeout' => env('LLM_OCR_GEMINI_TIMEOUT', 60),
    ],
];
