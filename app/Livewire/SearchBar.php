<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $isOpen = false;

    public function updatedQuery(): void
    {
        $clean = trim($this->query);
        if (strlen($clean) >= 2) {
            $this->results = Product::where('nom', 'like', "%{$clean}%")
                ->orWhere('reference', 'like', "%{$clean}%")
                ->orWhere('marque', 'like', "%{$clean}%")
                ->take(6)
                ->get()
                ->toArray();
            $this->isOpen = true;
        } else {
            $this->results = [];
            $this->isOpen = false;
        }
    }

    public function resetSearch(): void
    {
        $this->query = '';
        $this->results = [];
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
