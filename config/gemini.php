<?php

declare(strict_types=1);

return [
    'api_key' => env('GEMINI_API_KEY'),
    'base_url' => env('GEMINI_BASE_URL'),
    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 30),
    
    // Tambahkan baris ini di bawah
    'version' => 'v1', 
];