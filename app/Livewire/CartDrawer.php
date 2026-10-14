<?php

namespace App\Livewire;

use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;
    public array $items = [];

    protected $listeners = [
        'open-cart' => 'open',
        'close-cart' => 'close',
        'cart-updated' => 'syncItems'
    ];

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function syncItems(array $items): void
    {
        $this->items = $items;
    }

    public function render()
    {
        return view('livewire.cart-drawer');
    }
}
