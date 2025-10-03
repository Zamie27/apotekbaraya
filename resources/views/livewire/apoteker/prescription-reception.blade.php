<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Penerimaan Pesanan dari Resep</h1>
            <p class="text-gray-600 mt-1">Kelola resep yang masuk dan buat pesanan untuk pelanggan</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Cari Resep</span>
                </label>
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="No. resep, nama pasien, atau dokter..." 
                       class="input input-bordered w-full">
            </div>

            <!-- Status Filter -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Status</span>
                </label>
                <select wire:model.live="statusFilter" class="select select-bordered w-full">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Konfirmasi</option>
                    <option value="confirmed">Dikonfirmasi</option>
                    <option value="processed">Diproses</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="flex items-end">
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Total Resep</div>
                        <div class="stat-value text-primary">{{ $prescriptions->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Prescriptions Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>No. Resep</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Tanggal Upload</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $prescription)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $prescription->prescription_number }}</div>
                                <div class="text-sm text-gray-500">{{ $prescription->user->name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $prescription->patient_name }}</div>
                            </td>
                            <td>{{ $prescription->doctor_name }}</td>
                            <td>
                                <div class="text-sm">{{ $prescription->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>
                                <div class="badge {{ $prescription->status_badge_color }}">
                                    {{ $prescription->status_text }}
                                </div>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <!-- View Prescription Image -->
                                    <button class="btn btn-sm btn-outline btn-info" 
                                            onclick="viewPrescriptionModal{{ $prescription->prescription_id }}.showModal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat
                                    </button>

                                    <!-- Process Prescription -->
                                    @if($prescription->status === 'pending')
                                        <button class="btn btn-sm btn-primary" 
                                                wire:click="selectPrescription({{ $prescription->prescription_id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Proses
                                        </button>
                                    @endif

                                    @if($prescription->order_id)
                                        <a href="{{ route('apoteker.orders') }}" class="btn btn-sm btn-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Lihat Pesanan
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Prescription Image Modal -->
                        <dialog id="viewPrescriptionModal{{ $prescription->prescription_id }}" class="modal">
                            <div class="modal-box w-11/12 max-w-3xl">
                                <h3 class="font-bold text-lg mb-4">Resep - {{ $prescription->prescription_number }}</h3>
                                
                                <!-- Prescription Image -->
                                <div class="mb-4">
                                    <img src="{{ $prescription->image_url }}" 
                                         alt="Resep {{ $prescription->prescription_number }}" 
                                         class="w-full h-auto rounded-lg border">
                                </div>
                                
                                <!-- Prescription Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                    <div>
                                        <strong>Pasien:</strong> {{ $prescription->patient_name }}
                                    </div>
                                    <div>
                                        <strong>Dokter:</strong> {{ $prescription->doctor_name }}
                                    </div>
                                    <div>
                                        <strong>Metode Pengambilan:</strong> 
                                        <span class="badge {{ $prescription->delivery_method === 'delivery' ? 'badge-info' : 'badge-success' }}">
                                            {{ $prescription->delivery_method === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Apotek' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Tanggal Upload:</strong> {{ $prescription->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                <!-- Delivery Address (if delivery method) -->
                                @if($prescription->delivery_method === 'delivery' && $prescription->delivery_address)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                        <h4 class="font-semibold text-blue-800 mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Alamat Pengiriman
                                        </h4>
                                        @php
                                            $address = $prescription->delivery_address;
                                        @endphp
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <strong>Penerima:</strong> {{ $address['recipient_name'] ?? $prescription->patient_name }}
                                            </div>
                                            <div>
                                                <strong>Telepon:</strong> {{ $address['phone'] ?? 'Tidak ada' }}
                                            </div>
                                            <div class="md:col-span-2">
                                                <strong>Alamat Lengkap:</strong><br>
                                                {{ $address['address'] ?? '' }}<br>
                                                {{ $address['village'] ?? '' }}, {{ is_array($address['district'] ?? '') ? implode(', ', $address['district']) : ($address['district'] ?? '') }}<br>
                                                {{ is_array($address['city'] ?? '') ? implode(', ', $address['city']) : ($address['city'] ?? '') }}, {{ $address['province'] ?? '' }} {{ $address['postal_code'] ?? '' }}
                                            </div>
                                            @if(!empty($address['notes']))
                                                <div class="md:col-span-2">
                                                    <strong>Catatan Alamat:</strong> {{ $address['notes'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Prescription Notes -->
                                @if($prescription->notes)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                        <h4 class="font-semibold text-gray-800 mb-2">Catatan Resep</h4>
                                        <p class="text-sm text-gray-700">{{ $prescription->notes }}</p>
                                    </div>
                                @endif

                                <div class="modal-action">
                                    <form method="dialog">
                                        <button class="btn">Tutup</button>
                                    </form>
                                </div>
                            </div>
                        </dialog>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p>Tidak ada resep yang ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($prescriptions->hasPages())
            <div class="p-4 border-t">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>

    <!-- Product Selection Modal -->
    @if($showProductModal && $selectedPrescription)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg w-11/12 max-w-4xl max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Buat Pesanan dari Resep</h3>
                            <p class="text-sm text-gray-600">{{ $selectedPrescription->prescription_number }} - {{ $selectedPrescription->patient_name }}</p>
                        </div>
                        <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Product Selection -->
                        <div>
                            <h4 class="font-semibold mb-3">Pilih Produk</h4>
                            
                            <!-- Product Search -->
                            <div class="mb-4">
                                <input type="text" 
                                       wire:model.live="productSearch" 
                                       placeholder="Cari produk..." 
                                       class="input input-bordered w-full">
                            </div>

                            <!-- Product List -->
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach($products as $product)
                                    <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-600">
                                                Rp {{ number_format($product->price, 0, ',', '.') }} | Stok: {{ $product->quantity }}
                                            </div>
                                        </div>
                                        <button wire:click="addProduct({{ $product->product_id }})" 
                                                class="btn btn-sm btn-primary"
                                                @if($product->quantity <= 0) disabled @endif>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Selected Products -->
                        <div>
                            <h4 class="font-semibold mb-3">Produk Terpilih</h4>
                            
                            @if(empty($selectedProducts))
                                <div class="text-center py-8 text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <p>Belum ada produk yang dipilih</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($selectedProducts as $index => $item)
                                        <div class="flex items-center justify-between p-3 border rounded-lg">
                                            <div class="flex-1">
                                                <div class="font-medium">{{ $item['name'] }}</div>
                                                <div class="text-sm text-gray-600">
                                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input type="number" 
                                                       wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                       value="{{ $item['quantity'] }}" 
                                                       min="1" 
                                                       max="{{ $item['available_stock'] }}"
                                                       class="input input-bordered input-sm w-16">
                                                <button wire:click="removeProduct({{ $index }})" 
                                                        class="btn btn-sm btn-error btn-outline">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Total -->
                                    <div class="border-t pt-3">
                                        <div class="flex justify-between items-center font-semibold">
                                            <span>Total:</span>
                                            <span>Rp {{ number_format(collect($selectedProducts)->sum(function($item) { return $item['price'] * $item['quantity']; }), 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 border-t">
                    <div class="flex justify-end gap-3">
                        <button wire:click="closeModal" class="btn btn-outline">Batal</button>
                        <button wire:click="createOrder" 
                                class="btn btn-primary"
                                @if(empty($selectedProducts)) disabled @endif>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>