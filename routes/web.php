<?php

use Illuminate\Support\Facades\Route;

Route::get('/yandex-yml', function () {
    $client = new App\Services\YandexMarket\YmlYandex();
    return response($client->apply(), 200, ['Content-Type' => 'application/xml']);
});
