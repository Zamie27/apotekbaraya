<?php

namespace App\Livewire\Kurir;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.kurir')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.kurir.dashboard');
    }
}
