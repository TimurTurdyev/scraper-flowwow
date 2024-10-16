<?php

return [
    'flowwow_shop' => env('FLOWWOW_SHOP'),
    'flowwow_shop_id' => env('FLOWWOW_SHOP_ID'),
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
    'ozon' => [
        'client_id' => env('OZON_CLIENT_ID'),
        'api_key' => env('OZON_API_KEY'),
    ],
    'shop' => [
        'name' => env('SHOP_NAME'),
        'company' => env('SHOP_COMPANY'),
        'phone' => env('SHOP_PHONE'),
    ]
];
