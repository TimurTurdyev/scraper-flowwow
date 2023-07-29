<?php

return [
    'flowwow_shop' => env('FLOWWOW_SHOP'),
    'selenium' => [
        'chrome' => [
            'host' => env('SELENIUM_HOST', 'http://localhost'),
            'port' => env('SELENIUM_PORT', '4444'),
        ],
    ],
];
