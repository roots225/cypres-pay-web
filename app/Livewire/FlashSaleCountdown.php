<?php

namespace App\Livewire;

use Livewire\Component;

class FlashSaleCountdown extends Component
{
    public int $hours = 8;
    public int $minutes = 42;
    public int $seconds = 15;

    public function render()
    {
        return view('livewire.flash-sale-countdown');
    }
}
