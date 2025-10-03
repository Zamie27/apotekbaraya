<x-layouts.user title="Detail Resep">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Resep</h1>
                <p class="text-gray-600">Nomor Resep: {{ $prescription->prescription_number }}</p>
            </div>
            <a href="{{ route('customer.prescriptions.index') }}" class="btn btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Prescription Details -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Resep</h2>
                
                <div class="space-y-4">
                    <!-- Status -->
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="badge {{ $prescription->getStatusBadgeColorAttribute() }} badge-lg">
                            {{ $prescription->getStatusTextAttribute() }}
                        </span>
                    </div>

                    <!-- Doctor Name -->
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Nama Dokter:</span>
                        <span class="text-gray-900">{{ $prescription->doctor_name }}</span>
                    </div>

                    <!-- Patient Name -->
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Nama Pasien:</span>
                        <span class="text-gray-900">{{ $prescription->patient_name }}</span>
                    </div>

                    <!-- Upload Date -->
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Tanggal Upload:</span>
                        <span class="text-gray-900">{{ $prescription->created_at->format('d M Y, H:i') }}</span>
                    </div>

                    <!-- Delivery Method -->
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Metode Pengambilan:</span>
                        <span class="badge {{ $prescription->delivery_method === 'delivery' ? 'badge-info' : 'badge-success' }}">
                            {{ $prescription->delivery_method === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Toko' }}
                        </span>
                    </div>

                    @if($prescription->confirmed_at)
                    <!-- Confirmation Date -->
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Tanggal Konfirmasi:</span>
                        <span class="text-gray-900">{{ $prescription->confirmed_at->format('d M Y, H:i') }}</span>
                    </div>
                    @endif

                    @if($prescription->confirmedBy)
                    <!-- Confirmed By -->
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-700">Dikonfirmasi oleh:</span>
                        <span class="text-gray-900">{{ $prescription->confirmedBy->name }}</span>
                    </div>
                    @endif
                </div>

                <!-- Customer Notes -->
                @if($prescription->notes)
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-2">Catatan Anda:</h3>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-800">{{ $prescription->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Delivery Address -->
                @if($prescription->delivery_method === 'delivery' && $prescription->delivery_address)
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-2">Alamat Pengiriman:</h3>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="space-y-1 text-sm">
                            <p class="font-medium text-gray-900">{{ $prescription->delivery_address['recipient_name'] }}</p>
                            <p class="text-gray-700">{{ $prescription->delivery_address['phone'] }}</p>
                            <p class="text-gray-700">{{ $prescription->delivery_address['detailed_address'] }}</p>
                            <p class="text-gray-600">
                                {{ $prescription->delivery_address['village'] }}, 
                                {{ is_array($prescription->delivery_address['sub_district']) ? implode(', ', $prescription->delivery_address['sub_district']) : $prescription->delivery_address['sub_district'] }}, 
                                {{ is_array($prescription->delivery_address['regency']) ? implode(', ', $prescription->delivery_address['regency']) : $prescription->delivery_address['regency'] }}, 
                                {{ $prescription->delivery_address['province'] }} 
                                {{ $prescription->delivery_address['postal_code'] }}
                            </p>
                            @if(isset($prescription->delivery_address['notes']) && $prescription->delivery_address['notes'])
                                <p class="text-gray-600 italic">{{ $prescription->delivery_address['notes'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Apoteker Notes -->
                @if($prescription->confirmation_notes)
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-2">Catatan Apoteker:</h3>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-blue-800">{{ $prescription->confirmation_notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Prescription Image -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Foto Resep</h2>
                
                <div class="text-center">
                    <img src="{{ $prescription->getImageUrlAttribute() }}" 
                         alt="Resep {{ $prescription->prescription_number }}"
                         class="max-w-full h-auto rounded-lg shadow-md mx-auto">
                    
                    <div class="mt-4">
                        <a href="{{ $prescription->getImageUrlAttribute() }}" 
                           target="_blank" 
                           class="btn btn-outline btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Lihat Ukuran Penuh
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Information -->
        <div class="mt-6">
            @if($prescription->status === 'pending')
                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Menunggu Konfirmasi</h3>
                        <div class="text-sm">Resep Anda sedang dalam proses verifikasi oleh apoteker. Kami akan mengirimkan notifikasi setelah resep dikonfirmasi.</div>
                    </div>
                </div>
            @elseif($prescription->status === 'confirmed')
                <div class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Resep Dikonfirmasi</h3>
                        <div class="text-sm">Resep Anda telah dikonfirmasi oleh apoteker. Silakan lanjutkan untuk membuat pesanan obat.</div>
                    </div>
                </div>
                
                @if(!$prescription->order_id)
                <div class="mt-4 text-center">
                    <a href="#" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Buat Pesanan dari Resep
                    </a>
                </div>
                @endif
            @elseif($prescription->status === 'rejected')
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Resep Ditolak</h3>
                        <div class="text-sm">Maaf, resep Anda tidak dapat diproses. Silakan periksa catatan apoteker atau hubungi kami untuk informasi lebih lanjut.</div>
                    </div>
                </div>
            @elseif($prescription->status === 'processed')
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Resep Diproses</h3>
                        <div class="text-sm">Resep Anda telah diproses dan pesanan telah dibuat. Silakan cek status pesanan Anda.</div>
                    </div>
                </div>
                
                @if($prescription->order_id)
                <div class="mt-4 text-center">
                    <a href="{{ route('pelanggan.orders.show', $prescription->order_id) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Lihat Pesanan
                    </a>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
</x-layouts.user>