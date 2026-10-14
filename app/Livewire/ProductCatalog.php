<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $selectedCategory = '';
    public string $selectedBrand = '';
    public ?int $priceMin = null;
    public ?int $priceMax = null;
    public bool $onlyInStock = false;
    public string $search = '';
    public string $sort = 'recent';
    public string $viewMode = 'grid'; // 'grid' ou 'list'

    protected $queryString = [
        'selectedCategory' => ['except' => '', 'as' => 'cat'],
        'selectedBrand' => ['except' => '', 'as' => 'marque'],
        'priceMin' => ['except' => null, 'as' => 'pmin'],
        'priceMax' => ['except' => null, 'as' => 'pmax'],
        'onlyInStock' => ['except' => false, 'as' => 'instock'],
        'search' => ['except' => '', 'as' => 'q'],
        'sort' => ['except' => 'recent'],
    ];

    public function mount(?string $categorySlug = null): void
    {
        if ($categorySlug) {
            $this->selectedCategory = $categorySlug;
        }
        if (request()->has('q')) {
            $this->search = request()->get('q');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function filterCategory(string $slug): void
    {
        $this->selectedCategory = $slug;
        $this->resetPage();
    }

    public function filterBrand(string $brand): void
    {
        $this->selectedBrand = ($this->selectedBrand === $brand) ? '' : $brand;
        $this->resetPage();
    }

    public function setPriceRange(?int $min, ?int $max): void
    {
        $this->priceMin = $min;
        $this->priceMax = $max;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->selectedCategory = '';
        $this->selectedBrand = '';
        $this->priceMin = null;
        $this->priceMax = null;
        $this->onlyInStock = false;
        $this->search = '';
        $this->sort = 'recent';
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::query()->with('category');

        if (!empty($this->selectedCategory)) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategory);
            });
        }

        if (!empty($this->selectedBrand)) {
            $query->where('marque', $this->selectedBrand);
        }

        if (!empty($this->search)) {
            $clean = $this->search;
            $query->where(function ($q) use ($clean) {
                $q->where('nom', 'like', "%{$clean}%")
                  ->orWhere('reference', 'like', "%{$clean}%")
                  ->orWhere('marque', 'like', "%{$clean}%");
            });
        }

        if ($this->priceMin !== null) {
            $query->where('prix_vente', '>=', $this->priceMin);
        }

        if ($this->priceMax !== null && $this->priceMax > 0) {
            $query->where('prix_vente', '<=', $this->priceMax);
        }

        if ($this->onlyInStock) {
            $query->where('en_stock', true);
        }

        switch ($this->sort) {
            case 'prix_asc':
                $query->orderBy('prix_vente', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('prix_vente', 'desc');
                break;
            case 'popular':
                $query->orderBy('is_vedette', 'desc')->orderBy('id', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::withCount('products')->orderBy('ordre')->get();
        $brands = Product::whereNotNull('marque')->distinct()->pluck('marque')->filter()->values();

        return view('livewire.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
