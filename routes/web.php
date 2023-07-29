<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/yandex-yml', function () {
    $content = Cache::rememberForever('yml_yandex', function () {
        $client = new App\Services\YandexMarket\YmlYandex();
        return $client->apply();
    });

    return response($content, 200, ['Content-Type' => 'application/xml']);
});
