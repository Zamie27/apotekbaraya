<?php

namespace App\Livewire;

use App\Models\Prescription;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PrescriptionCounter extends Component
{
    /**
     * Get the count of active prescriptions for the authenticated user
     * Active prescriptions are those that are pending or confirmed (waiting for action)
     */
    public function getActivePrescriptionsCountProperty()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Prescription::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    /**
     * Render the prescription counter component
     */
    public function render()
    {
        return view('livewire.prescription-counter');
    }
}