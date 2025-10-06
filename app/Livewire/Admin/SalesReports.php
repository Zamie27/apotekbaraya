<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Laporan Penjualan')]
class SalesReports extends Component
{
    use WithPagination;

    public string $dateRange = '30'; // days
    public ?string $status = 'completed';
    public ?int $perPage = 10;

    protected $queryString = [
        'dateRange' => ['except' => '30'],
        'status' => ['except' => 'completed'],
        'perPage' => ['except' => 10],
    ];

    public function getStatusOptionsProperty(): array
    {
        return [
            'all' => 'Semua',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'delivered' => 'Selesai',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public function render()
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays((int) $this->dateRange);

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [$start, $end]);

        if ($this->status && $this->status !== 'all') {
            $ordersQuery->where('status', $this->status);
        }

        $orders = $ordersQuery
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        $orderIds = (clone $ordersQuery)->pluck('order_id');
        $summary = [
            'orders_count' => (clone $ordersQuery)->count(),
            'items_count' => OrderItem::whereIn('order_id', $orderIds)->sum('qty'),
            'revenue' => (clone $ordersQuery)->sum('total_price'),
            'delivery_fee' => (clone $ordersQuery)->sum('delivery_fee'),
            'discount' => (clone $ordersQuery)->sum('discount_amount'),
            'subtotal' => (clone $ordersQuery)->sum('subtotal'),
        ];

        $daily = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'), DB::raw('COUNT(*) as orders'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('livewire.admin.sales-reports', [
            'orders' => $orders,
            'summary' => $summary,
            'daily' => $daily,
            'start' => $start,
            'end' => $end,
        ]);
    }
}