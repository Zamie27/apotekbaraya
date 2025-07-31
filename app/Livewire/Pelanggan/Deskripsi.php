<?php

namespace App\Livewire\Pelanggan;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.user')]
class Deskripsi extends Component
{
    public function render()
    {
        return view('livewire.pelanggan.deskripsi');
    }
}
