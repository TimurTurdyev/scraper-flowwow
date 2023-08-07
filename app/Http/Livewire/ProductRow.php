<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ProductRow extends Component
{
    public ?Product $product = null;

    public bool $yandex = false;
    public bool $ozon = false;

    public function mount(): void
    {
        $this->yandex = $this->product->yandex;
        $this->ozon = $this->product->ozon;
    }

    public function updatedYandex(): void
    {
        $this->product->update(['yandex' => $this->yandex]);
        Cache::forget('yml_yandex');
        Cache::forget('yml_ozon');
    }

    public function updatedOzon(): void
    {
        $this->product->update(['ozon' => $this->ozon]);
        Cache::forget('yml_yandex');
        Cache::forget('yml_ozon');
    }

    public function render(): View
    {
        return view('livewire.product-row');
    }
}
