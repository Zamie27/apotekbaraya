<div class="p-6 space-y-6">
    <div class="flex items-end gap-4">
        <div>
            <label class="block text-sm font-medium">Rentang Hari</label>
            <input type="number" min="1" max="365" wire:model.live="dateRange" class="input input-bordered w-28" />
        </div>
        <div>
            <label class="block text-sm font-medium">Metode Pembayaran</label>
            <select wire:model.live="method" class="select select-bordered">
                @foreach($this->methodOptions as $key => $label)
                    <option value="{{ $key }}">{{ is_string($label) ? $label : strtoupper($key) }}</option>
                @endforeach
            </select>
        </div>
        <div class="ml-auto text-sm text-gray-500">Periode: {{ $start->toDateString() }} s/d {{ $end->toDateString() }}</div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Ringkasan per Metode</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Metode</th>
                            <th>Jumlah Pesanan</th>
                            <th>Total Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryByMethod as $row)
                            <tr>
                                <td>{{ strtoupper($row->payment_method_code) }}</td>
                                <td>{{ number_format($row->orders) }}</td>
                                <td>Rp {{ number_format($row->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Ringkasan Status</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Jumlah Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statusSummary as $row)
                            <tr>
                                <td>{{ strtoupper($row->status) }}</td>
                                <td>{{ number_format($row->orders) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Metode</th>
                            <th>Status Pembayaran</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->order_number ?? $order->order_id }}</td>
                                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ strtoupper($order->payment_method_code) }}</td>
                                <td><span class="badge {{ $order->status_badge_color }}">{{ $order->payment_status }}</span></td>
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