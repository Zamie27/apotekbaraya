<x-layouts.user title="Riwayat Resep">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Riwayat Resep</h1>
                <p class="text-gray-600">Kelola dan pantau status resep yang telah Anda upload</p>
            </div>
            <a href="{{ route('customer.prescriptions.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Resep Baru
            </a>
        </div>

        @if($prescriptions->count() > 0)
            <!-- Prescriptions Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($prescriptions as $prescription)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <!-- Prescription Image -->
                    <div class="h-48 bg-gray-100 overflow-hidden">
                        <img src="{{ $prescription->getImageUrlAttribute() }}" 
                             alt="Resep {{ $prescription->prescription_number }}"
                             class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Prescription Info -->
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-800 truncate">{{ $prescription->prescription_number }}</h3>
                            <span class="badge {{ $prescription->getStatusBadgeColorAttribute() }} badge-sm">
                                {{ $prescription->getStatusTextAttribute() }}
                            </span>
                        </div>
                        
                        <div class="space-y-1 text-sm text-gray-600 mb-4">
                            <p><span class="font-medium">Dokter:</span> {{ $prescription->doctor_name }}</p>
                            <p><span class="font-medium">Pasien:</span> {{ $prescription->patient_name }}</p>
                            <p><span class="font-medium">Upload:</span> {{ $prescription->created_at->format('d M Y') }}</p>
                            @if($prescription->confirmed_at)
                            <p><span class="font-medium">Konfirmasi:</span> {{ $prescription->confirmed_at->format('d M Y') }}</p>
                            @endif
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <a href="{{ route('customer.prescriptions.show', $prescription->id) }}" 
                               class="btn btn-outline btn-sm flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </a>
                            
                            @if($prescription->status === 'confirmed' && !$prescription->order_id)
                            <a href="#" class="btn btn-primary btn-sm flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Pesan
                            </a>
                            @elseif($prescription->order_id)
                            <a href="{{ route('pelanggan.orders.show', $prescription->order_id) }}" 
                               class="btn btn-info btn-sm flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Pesanan
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $prescriptions->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Resep</h3>
                    <p class="text-gray-600 mb-6">Anda belum pernah mengupload resep. Upload resep pertama Anda untuk mendapatkan obat yang dibutuhkan.</p>
                    <a href="{{ route('customer.prescriptions.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Upload Resep Pertama
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</x-layouts.user>