<?php

return [
    'flowwow_shop' => env('FLOWWOW_SHOP'),
    'selenium' => [
        'chrome' => [
            'host' => env('SELENIUM_HOST', 'http://localhost'),
            'port' => env('SELENIUM_PORT', '4444'),
        ],
    ],
    'yandex' => [
        'device_id' => env('YANDEX_DEVICE_ID'),
        'client_id' => env('YANDEX_CLIENT_ID'),
        'client_secret' => env('YANDEX_CLIENT_SECRET'),
    ],
];
