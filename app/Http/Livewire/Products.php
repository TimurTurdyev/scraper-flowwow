<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public function render(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('category_id')
            ->paginate(50);

        return view('livewire.products', [
            'products' => $products
        ]);
    }
}
