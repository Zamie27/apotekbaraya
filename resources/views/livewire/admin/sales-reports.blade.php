<div class="p-6 space-y-6">
    <div class="flex items-end gap-4">
        <div>
            <label class="block text-sm font-medium">Rentang Hari</label>
            <input type="number" min="1" max="365" wire:model.live="dateRange" class="input input-bordered w-28" />
        </div>
        <div>
            <label class="block text-sm font-medium">Status</label>
            <select wire:model.live="status" class="select select-bordered">
                @foreach($this->statusOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="ml-auto text-sm text-gray-500">Periode: {{ $start->toDateString() }} s/d {{ $end->toDateString() }}</div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="text-sm">Total Pesanan</div>
                <div class="text-2xl font-semibold">{{ number_format($summary['orders_count']) }}</div>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="text-sm">Total Item</div>
                <div class="text-2xl font-semibold">{{ number_format($summary['items_count']) }}</div>
            </div>
        </div>
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="text-sm">Pendapatan</div>
                <div class="text-2xl font-semibold">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Ringkasan Harian</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pesanan</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daily as $row)
                            <tr>
                                <td>{{ $row->date }}</td>
                                <td>{{ number_format($row->orders) }}</td>
                                <td>Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Daftar Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Subtotal</th>
                            <th>Diskon</th>
                            <th>Ongkir</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->code ?? $order->id }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status_badge_color }}">{{ $order->status_label }}</span>
                                </td>
                                <td>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
                                <td class="font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</div>