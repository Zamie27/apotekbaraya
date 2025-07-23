<?php

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.pelanggan')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.pelanggan.dashboard');
    }
}
