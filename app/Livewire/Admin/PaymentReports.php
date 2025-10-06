<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
// Ambil metode pembayaran dari data Order secara dinamis
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Laporan Pembayaran')]
class PaymentReports extends Component
{
    use WithPagination;

    public string $dateRange = '30';
    public ?string $method = 'all';
    public ?int $perPage = 10;

    protected $queryString = [
        'dateRange' => ['except' => '30'],
        'method' => ['except' => 'all'],
        'perPage' => ['except' => 10],
    ];

    public function getMethodOptionsProperty(): array
    {
        $codes = Order::select('payment_method_code')
            ->whereNotNull('payment_method_code')
            ->distinct()
            ->pluck('payment_method_code')
            ->toArray();

        $options = ['all' => 'Semua Metode'];
        foreach ($codes as $code) {
            $options[$code] = strtoupper($code);
        }

        return $options;
    }

    public function render()
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays((int) $this->dateRange);

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('payment_method_code');

        if ($this->method && $this->method !== 'all') {
            $ordersQuery->where('payment_method_code', $this->method);
        }

        $orders = $ordersQuery->orderByDesc('created_at')->paginate($this->perPage);

        $summaryByMethod = Order::select('payment_method_code', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total_price) as amount'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('payment_method_code')
            ->groupBy('payment_method_code')
            ->get();

        $statusSummary = Order::select('status', DB::raw('COUNT(*) as orders'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->get();

        return view('livewire.admin.payment-reports', [
            'orders' => $orders,
            'summaryByMethod' => $summaryByMethod,
            'statusSummary' => $statusSummary,
            'start' => $start,
            'end' => $end,
        ]);
    }
}