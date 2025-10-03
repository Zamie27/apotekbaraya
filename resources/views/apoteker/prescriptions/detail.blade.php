<x-layouts.apoteker title="Detail Resep - {{ $prescription->prescription_number }}">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('apoteker.prescriptions.manage') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $prescription->prescription_number }}</h1>
                    <p class="text-gray-600">Detail resep dari pelanggan</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                @if($prescription->status === 'pending')
                <button onclick="openConfirmModal()" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Konfirmasi Resep
                </button>
                @endif
                
                @if($prescription->status === 'confirmed' && !$prescription->order_id)
                <a href="{{ route('apoteker.prescriptions.create-order', $prescription) }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Buat Pesanan
                </a>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Prescription Image -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b">
                        <h2 class="text-lg font-semibold text-gray-800">Gambar Resep</h2>
                    </div>
                    <div class="p-4">
                        <div class="bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ $prescription->getImageUrlAttribute() }}" 
                                 alt="Resep {{ $prescription->prescription_number }}"
                                 class="w-full h-auto max-h-96 object-contain mx-auto"
                                 onclick="openImageModal(this.src)">
                        </div>
                        <p class="text-sm text-gray-500 mt-2 text-center">Klik gambar untuk memperbesar</p>
                    </div>
                </div>
            </div>

            <!-- Prescription Details -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Resep</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="badge {{ $prescription->getStatusBadgeColorAttribute() }} badge-lg">
                                {{ $prescription->getStatusTextAttribute() }}
                            </span>
                        </div>
                        
                        @if($prescription->confirmed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Dikonfirmasi:</span>
                            <span class="text-sm text-gray-800">{{ $prescription->confirmed_at->format('d M Y H:i') }}</span>
                        </div>
                        @endif
                        
                        @if($prescription->confirmedBy)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Oleh:</span>
                            <span class="text-sm text-gray-800">{{ $prescription->confirmedBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pelanggan</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Nama Pelanggan:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Email:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Telepon:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Prescription Info -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Resep</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Nomor Resep:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->prescription_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Nama Dokter:</label>
                            <p class="font-medium text-gray-800">Dr. {{ $prescription->doctor_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Nama Pasien:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->patient_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Tanggal Upload:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Metode Pengambilan:</label>
                            <div class="flex items-center mt-1">
                                @if($prescription->delivery_method === 'pickup')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Ambil di Toko
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        Kirim ke Alamat
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Delivery Address Information -->
                        @if($prescription->delivery_method === 'delivery' && $prescription->delivery_address)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Alamat Pengiriman
                            </h4>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium text-blue-800">{{ $prescription->delivery_address['recipient_name'] }}</span>
                                    <span class="text-blue-600 ml-2">{{ $prescription->delivery_address['phone'] }}</span>
                                </div>
                                <p class="text-blue-700">
                                    {{ $prescription->delivery_address['detailed_address'] ?? $prescription->delivery_address['address'] ?? '' }}
                                </p>
                                <p class="text-blue-600">
                                    {{ $prescription->delivery_address['village'] }}, 
                                    {{ is_array($prescription->delivery_address['sub_district'] ?? $prescription->delivery_address['district'] ?? '') ? implode(', ', $prescription->delivery_address['sub_district'] ?? $prescription->delivery_address['district'] ?? '') : ($prescription->delivery_address['sub_district'] ?? $prescription->delivery_address['district'] ?? '') }}, 
                                    {{ is_array($prescription->delivery_address['regency'] ?? $prescription->delivery_address['city'] ?? '') ? implode(', ', $prescription->delivery_address['regency'] ?? $prescription->delivery_address['city'] ?? '') : ($prescription->delivery_address['regency'] ?? $prescription->delivery_address['city'] ?? '') }}, 
                                    {{ $prescription->delivery_address['province'] }} 
                                    {{ $prescription->delivery_address['postal_code'] }}
                                </p>
                                @if(isset($prescription->delivery_address['notes']) && $prescription->delivery_address['notes'])
                                    <p class="text-blue-600 italic">{{ $prescription->delivery_address['notes'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                @if($prescription->notes)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan Pelanggan</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $prescription->notes }}</p>
                </div>
                @endif

                <!-- Order Information -->
                @if($prescription->order_id && $prescription->order)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pesanan</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Nomor Pesanan:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->order->order_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Total Pesanan:</label>
                            <p class="font-medium text-gray-800">Rp {{ number_format($prescription->order->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Status Pesanan:</label>
                            <span class="badge badge-info">{{ ucfirst($prescription->order->status) }}</span>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Status Pembayaran:</label>
                            <span class="badge badge-warning">{{ ucfirst($prescription->order->payment_status) }}</span>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Tanggal Pesanan:</label>
                            <p class="font-medium text-gray-800">{{ $prescription->order->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('apoteker.orders.detail', $prescription->order->id) }}" class="btn btn-outline btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat Detail Pesanan
                        </a>
                    </div>
                </div>
                @endif

                <!-- Confirmation Notes -->
                @if($prescription->confirmation_notes)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Catatan Konfirmasi</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $prescription->confirmation_notes }}</p>
                </div>
                @endif

                <!-- Order Link -->
                @if($prescription->order_id)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pesanan Terkait</h3>
                    <a href="{{ route('apoteker.orders.detail', $prescription->order_id) }}" 
                       class="btn btn-outline w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Lihat Pesanan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal">
    <div class="modal-box max-w-4xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Gambar Resep</h3>
            <button class="btn btn-sm btn-circle" onclick="closeImageModal()">✕</button>
        </div>
        <div class="text-center">
            <img id="modalImage" src="" alt="Resep" class="max-w-full h-auto">
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
@if($prescription->status === 'pending')
<div id="confirmModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">Konfirmasi Resep</h3>
        <form method="POST" action="{{ route('apoteker.prescriptions.confirm', $prescription->getKey()) }}">
            @csrf
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Status Konfirmasi</span>
                </label>
                <select name="status" class="select select-bordered" required>
                    <option value="">Pilih Status</option>
                    <option value="confirmed">Konfirmasi - Resep Valid</option>
                    <option value="rejected">Tolak - Resep Tidak Valid</option>
                </select>
            </div>
            
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Catatan Konfirmasi</span>
                </label>
                <textarea name="confirmation_notes" 
                          class="textarea textarea-bordered" 
                          rows="3" 
                          placeholder="Berikan catatan mengenai konfirmasi resep ini..."></textarea>
            </div>
            
            <div class="modal-action">
                <button type="button" class="btn" onclick="closeConfirmModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Konfirmasi</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openImageModal(src) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modalImage.src = src;
    modal.classList.add('modal-open');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('modal-open');
}

@if($prescription->status === 'pending')
function openConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.add('modal-open');
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('modal-open');
}

// Close modals when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});
@endif

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
</x-layouts.apoteker>