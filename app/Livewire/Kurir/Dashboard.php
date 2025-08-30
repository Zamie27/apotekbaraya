<?php

namespace App\Livewire\Kurir;

use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.kurir')]
class Dashboard extends Component
{
    /**
     * Render the kurir dashboard with delivery statistics
     */
    public function render()
    {
        $courierId = Auth::id();
        
        // Get delivery statistics for current courier
        $pendingDeliveries = Delivery::byCourier($courierId)->byStatus('pending')->count();
        $inTransitDeliveries = Delivery::byCourier($courierId)->byStatus('in_transit')->count();
        $completedToday = Delivery::byCourier($courierId)
            ->byStatus('delivered')
            ->whereDate('delivered_at', today())
            ->count();
        $totalDeliveries = Delivery::byCourier($courierId)->count();
        
        // Get recent deliveries (last 5)
        $recentDeliveries = Delivery::with(['order.user'])
            ->byCourier($courierId)
            ->latest()
            ->take(5)
            ->get();
        
        return view('livewire.kurir.dashboard', [
            'pendingDeliveries' => $pendingDeliveries,
            'inTransitDeliveries' => $inTransitDeliveries,
            'completedToday' => $completedToday,
            'totalDeliveries' => $totalDeliveries,
            'recentDeliveries' => $recentDeliveries,
        ]);
    }
}
