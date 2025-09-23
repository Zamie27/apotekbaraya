<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class OrderCounter extends Component
{
    /**
     * Get the count of active orders for the authenticated user
     * Active orders are those that are not completed, cancelled, or delivered
     */
    public function getActiveOrdersCountProperty()
    {
        if (!Auth::check()) {
            return 0;
        }

        return Order::where('user_id', Auth::id())
            ->whereNotIn('status', ['completed', 'cancelled', 'delivered'])
            ->count();
    }

    public function render()
    {
        return view('livewire.order-counter');
    }
}
