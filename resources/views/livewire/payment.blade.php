<div class="min-h-screen bg-gray-50 py-4 sm:py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Pembayaran</h1>
            <p class="text-gray-600 mt-2">Selesaikan pembayaran untuk pesanan Anda</p>
        </div>

        @if($paymentUrl)
            <div class="max-w-2xl mx-auto">
                <!-- Redirect Message -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 text-center">
                    <div class="mb-6">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Mengarahkan ke Halaman Pembayaran</h2>
                        <p class="text-gray-600 mb-6">Anda akan diarahkan ke halaman pembayaran Midtrans untuk menyelesaikan transaksi.</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                        <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Pesanan</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Pesanan:</span>
                                <span class="font-medium">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Pembayaran:</span>
                                <span class="font-bold text-lg text-primary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Redirect Button -->
                    <div class="space-y-4">
                        <p class="text-sm text-gray-500">Jika Anda tidak diarahkan secara otomatis, klik tombol di bawah ini:</p>
                        <a href="{{ $paymentUrl }}" class="btn btn-primary btn-lg w-full sm:w-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2z" />
                            </svg>
                            Lanjutkan Pembayaran
                        </a>
                    </div>

                    <!-- Back to Orders -->
                    <div class="mt-6 pt-4 border-t">
                        <a href="{{ route('pelanggan.orders') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            ← Kembali ke Daftar Pesanan
                        </a>
                    </div>
                </div>
            </div>

        <!-- Auto Redirect Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto redirect after 3 seconds
                setTimeout(function() {
                    window.location.href = '{{ $paymentUrl }}';
                }, 3000);
            });
        </script>
    @endif
    </div>

    <!-- Custom CSS for Midtrans SNAP -->
    <style>
        #snap-container {
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Responsive adjustments for Midtrans SNAP */
        @media (max-width: 640px) {
            #snap-container {
                min-height: 450px !important;
            }
        }
        
        /* Ensure SNAP iframe is responsive */
        #snap-container iframe {
            width: 100% !important;
            border: none;
            border-radius: 8px;
        }
    </style>


</div>