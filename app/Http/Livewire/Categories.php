<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\OzonCategory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Categories extends Component
{
    public array $categories = [];
    public string $search = '';
    public array $matchCategories = [];
    public ?int $selected = null;
    public ?int $checkOzonCategory = null;

    public $listeners = ['editCategory'];

    public function mount(): void
    {
        $this->loadCategories();
    }

    public function editCategory($id): void
    {
        $this->selected = $id;
    }

    public function updatedSearch($search): void
    {
        $querySearch = str($search)
            ->replaceMatches('/[^\p{L}\p{N}]/u', '')
            ->lower()
            ->slug('', 'ru');

        if ($querySearch->length() < 3) {
            $this->matchCategories = [];
            return;
        }

        $search = $querySearch->toString();

        $this->matchCategories = OzonCategory::query()
            ->where('search', 'like', $search . '%')
            ->orWhere('search', 'like', '%' . $search . '%')
            ->orWhere('search', 'like', '%' . $search)
            ->limit(1000)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function saveOzonCategory(): void
    {
        if (
            $this->checkOzonCategory &&
            $this->selected &&
            $category = Category::query()->find($this->selected)
        ) {
            $category->update(['ozon_category_id' => $this->checkOzonCategory]);
            $this->checkOzonCategory = null;
            $this->loadCategories();
            $this->dispatchBrowserEvent('modalClose');
        }
    }

    public function render(): View
    {
        return view('livewire.categories');
    }

    private function loadCategories(): void
    {
        $this->categories = Category::query()
            ->with('ozonCategory')
            ->get()
            ->map(function (Category $category) {
                $data = $category->toArray();
                $data['updated_at'] = $category->updated_at->format('Y-m-d H:i:s');
                return $data;
            })->toArray();
    }
}
