<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\OzonCategory;
use App\Models\Product;
use App\Models\User;
use App\Services\Ozon\ProductInfoLimit;
use Livewire\Component;
use Symfony\Component\HttpClient\Psr18Client;

class Dashboard extends Component
{
    public array $statistics = [];
    public array $ozonStatistic = [];

    public function mount()
    {
        $this->statistics[] = [
            'name' => 'Категорий',
            'total' => Category::query()->count(),
            'icon' => 'ni ni-archive-2',
        ];

        $this->statistics[] = [
            'name' => 'Товаров',
            'total' => Product::query()->count(),
            'icon' => 'ni ni-archive-2',
        ];

        $this->statistics[] = [
            'name' => 'Пользователей',
            'total' => User::query()->count(),
            'icon' => 'ni ni-archive-2',
        ];

        $this->statistics[] = [
            'name' => 'Ozon категорий',
            'total' => OzonCategory::query()->count(),
            'icon' => 'ni ni-archive-2',
        ];

        $config = [
            'clientId' => config('scraper.ozon.client_id'),
            'apiKey' => config('scraper.ozon.api_key'),
            //'host' => 'http://cb-api.ozonru.me/'
        ];

        $client = new Psr18Client();
        $productService = new ProductInfoLimit($config, $client);
        $this->ozonStatistic = $productService->infoLimit();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
