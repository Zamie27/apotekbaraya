<?php

namespace App\Livewire\Apoteker;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.apoteker')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.apoteker.dashboard');
    }
}
