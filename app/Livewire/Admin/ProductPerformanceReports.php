<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Laporan Kinerja Produk')]
class ProductPerformanceReports extends Component
{
    use WithPagination;

    public string $dateRange = '30';
    public ?int $categoryId = null;
    public ?int $perPage = 10;

    protected $queryString = [
        'dateRange' => ['except' => '30'],
        'categoryId' => ['except' => null],
        'perPage' => ['except' => 10],
    ];

    public function render()
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays((int) $this->dateRange);

        $baseItemsQuery = OrderItem::query()
            ->whereBetween('created_at', [$start, $end])
            ->with('product');

        if ($this->categoryId) {
            $baseItemsQuery->whereHas('product', function($q) {
                $q->where('category_id', $this->categoryId);
            });
        }

        $productAgg = (clone $baseItemsQuery)
            ->select('product_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(qty * price) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->paginate($this->perPage);

        $topProducts = (clone $baseItemsQuery)
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Ambil daftar kategori dengan nama untuk dropdown filter
        $categories = Category::query()
            ->orderBy('name')
            ->get(['category_id', 'name']);

        return view('livewire.admin.product-performance-reports', [
            'productAgg' => $productAgg,
            'topProducts' => $topProducts,
            'categories' => $categories,
            'start' => $start,
            'end' => $end,
        ]);
    }
}