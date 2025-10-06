<div class="p-6 space-y-6">
    <div class="flex items-end gap-4">
        <div>
            <label class="block text-sm font-medium">Rentang Hari</label>
            <input type="number" min="1" max="365" wire:model.live="dateRange" class="input input-bordered w-28" />
        </div>
        <div>
            <label class="block text-sm font-medium">Kategori</label>
            <select wire:model.live="categoryId" class="select select-bordered">
                <option value="">Semua</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ml-auto text-sm text-gray-500">Periode: {{ $start->toDateString() }} s/d {{ $end->toDateString() }}</div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Top 5 Produk Berdasarkan Qty</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $row)
                            <tr>
                                <td>{{ optional(\App\Models\Product::find($row->product_id))->name ?? 'Produk #' . $row->product_id }}</td>
                                <td>{{ number_format($row->total_qty) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Performa Produk</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Total Qty</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productAgg as $row)
                            <tr>
                                <td>{{ optional(\App\Models\Product::find($row->product_id))->name ?? 'Produk #' . $row->product_id }}</td>
                                <td>{{ number_format($row->total_qty) }}</td>
                                <td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $productAgg->links() }}</div>
        </div>
    </div>
</div>