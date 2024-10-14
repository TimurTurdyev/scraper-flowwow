<?php

use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\ResetPassword;
use App\Http\Livewire\Auth\SignUp;
use App\Http\Livewire\Billing;
use App\Http\Livewire\Categories;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Products;
use App\Http\Livewire\Profile;
use App\Http\Livewire\Tables;
use App\Http\Livewire\User\UserManagement;
use App\Http\Livewire\User\UserProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/yandex-yml', function () {
    $content = Cache::rememberForever('yml_yandex', function () {
        $client = new App\Services\YandexMarket\YmlYandex();
        return $client->apply();
    });

    return response($content, 200, ['Content-Type' => 'application/xml']);
});

Route::get('/ozon-yml', function () {
    $content = Cache::rememberForever('yml_ozon', function () {
        $client = new \App\Services\Ozon\YmlOzon();
        return $client->apply();
    });

    return response($content, 200, ['Content-Type' => 'application/xml']);
});

Route::get('/', function () {
    return redirect('/login');
});

//Route::get('/sign-up', SignUp::class)->name('sign-up');
Route::get('/login', Login::class)->name('login');

Route::get('/login/forgot-password', ForgotPassword::class)->name('forgot-password');

Route::get('/reset-password/{id}', ResetPassword::class)->name('reset-password')->middleware('signed');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/categories', Categories::class)->name('categories');
    Route::get('/products', Products::class)->name('products');
    Route::get('/billing', Billing::class)->name('billing');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/tables', Tables::class)->name('tables');
    Route::get('/user/profile', UserProfile::class)->name('user-profile');
    Route::get('/user/management', UserManagement::class)->name('user-management');
});
