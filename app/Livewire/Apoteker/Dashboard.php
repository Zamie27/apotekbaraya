<?php

namespace App\Livewire\Apoteker;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.apoteker')]
class Dashboard extends Component
{
    /**
     * Render the apoteker dashboard with order statistics and recent orders
     */
    public function render()
    {
        // Get order statistics
        $waitingConfirmation = Order::where('status', 'waiting_confirmation')->count();
        $confirmed = Order::where('status', 'confirmed')->count();
        $processing = Order::where('status', 'processing')->count();
        $totalOrders = Order::count();
        
        // Get recent orders that need attention (waiting confirmation)
        $recentOrders = Order::with(['user', 'payment'])
            ->where('status', 'waiting_confirmation')
            ->latest()
            ->take(5)
            ->get();
        
        // Get all recent orders for general overview
        $allRecentOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('livewire.apoteker.dashboard', [
            'waitingConfirmation' => $waitingConfirmation,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'totalOrders' => $totalOrders,
            'recentOrders' => $recentOrders,
            'allRecentOrders' => $allRecentOrders,
        ]);
    }
}
