<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use App\Models\UserLog;
use App\Models\OrderItem;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    public $timeFilter = 'all'; // all, today, week, month
    
    /**
     * Update time filter and refresh statistics
     */
    public function setTimeFilter($filter)
    {
        $this->timeFilter = $filter;
    }
    
    /**
     * Get date range based on time filter
     */
    private function getDateRange()
    {
        switch ($this->timeFilter) {
            case 'today':
                return [Carbon::today(), Carbon::tomorrow()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            default:
                return [null, null];
        }
    }
    
    /**
     * Get total completed orders based on time filter
     */
    public function getTotalOrdersProperty()
    {
        [$startDate, $endDate] = $this->getDateRange();
        
        $query = Order::where('status', 'completed');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->count();
    }
    
    /**
     * Get total revenue from completed orders based on time filter
     */
    public function getTotalRevenueProperty()
    {
        [$startDate, $endDate] = $this->getDateRange();
        
        $query = Order::where('status', 'completed');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->sum('total_price') ?? 0;
    }
    
    /**
     * Get total products sold from completed orders based on time filter
     */
    public function getTotalProductsSoldProperty()
    {
        [$startDate, $endDate] = $this->getDateRange();
        
        $query = OrderItem::whereHas('order', function($q) {
            $q->where('status', 'completed');
        });
        
        if ($startDate && $endDate) {
            $query->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }
        
        return $query->sum('qty') ?? 0;
    }
    
    /**
     * Get total customers (users with role 'pelanggan')
     * Note: This always shows total registered customers, not filtered by time
     */
    public function getTotalCustomersProperty()
    {
        return User::whereHas('role', function($query) {
            $query->where('name', 'pelanggan');
        })->count();
    }
    
    /**
     * Get filter label for display
     */
    public function getFilterLabelProperty()
    {
        switch ($this->timeFilter) {
            case 'today':
                return 'Hari ini';
            case 'week':
                return '1 Minggu';
            case 'month':
                return '1 Bulan';
            default:
                return 'Keseluruhan';
        }
    }
    
    /**
     * Get recent activity logs with delivery details
     */
    public function getRecentActivitiesProperty()
    {
        return UserLog::with('user')
            ->whereIn('action', ['courier_assigned', 'order_shipped', 'order_delivered', 'delivery_cancelled'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalOrders' => $this->totalOrders,
            'totalRevenue' => $this->totalRevenue,
            'totalProductsSold' => $this->totalProductsSold,
            'totalCustomers' => $this->totalCustomers,
            'filterLabel' => $this->filterLabel,
            'recentActivities' => $this->recentActivities,
        ]);
    }
}
