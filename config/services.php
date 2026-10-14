<?php

return [
    'wave' => [
        'api_key' => env('WAVE_API_KEY'),
        'api_url' => env('WAVE_API_URL', 'https://api.wave.com/v1'),
    ],
    'orange_money' => [
        'api_url' => env('ORANGE_MONEY_API_URL'),
    ],
    'mtn_momo' => [
        'api_url' => env('MTN_MOMO_API_URL'),
    ],
];
