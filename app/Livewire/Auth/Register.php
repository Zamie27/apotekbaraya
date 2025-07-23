<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.user')]
class Register extends Component
{
    public function render()
    {
        return view('livewire.auth.register');
    }
}
