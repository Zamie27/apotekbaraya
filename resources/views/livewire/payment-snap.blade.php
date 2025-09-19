<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pembayaran Pesanan</h1>
                    <p class="text-gray-600 mt-1">Nomor Pesanan: <span class="font-semibold">{{ $order->order_number }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Instructions -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Instruksi Pembayaran</h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Klik tombol "Bayar Sekarang" untuk membuka halaman pembayaran</li>
                            <li>Pilih metode pembayaran yang Anda inginkan</li>
                            <li>Ikuti instruksi pembayaran sesuai metode yang dipilih</li>
                            <li>Setelah pembayaran berhasil, Anda akan diarahkan kembali ke halaman pesanan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Pesanan</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                    <p class="font-medium text-gray-900">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                </div>
                @endforeach

                <!-- Shipping Cost -->
                @if($order->shipping_cost > 0)
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <p class="text-gray-600">Biaya Pengiriman</p>
                    <p class="font-medium text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                </div>
                @endif

                <!-- Total -->
                <div class="flex justify-between items-center py-3 border-t border-gray-200">
                    <p class="text-lg font-semibold text-gray-900">Total</p>
                    <p class="text-lg font-bold text-success">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Button -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="text-center">
                <button id="pay-button" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Bayar Sekarang
                </button>

                <div class="mt-4">
                    <a href="{{ route('pelanggan.orders.show', $order->order_id) }}" class="text-sm text-gray-500 hover:text-gray-700">
                        Kembali ke Detail Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Midtrans SNAP JS -->
<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');

        // Listen for payment redirect events from Livewire
        window.addEventListener('payment-redirect', function(event) {
            console.log('Redirecting to:', event.detail.url);
            // Add small delay to ensure session flash messages are set
            setTimeout(function() {
                window.location.href = event.detail.url;
            }, 500);
        });

        payButton.addEventListener('click', function(e) {
            e.preventDefault();

            // Disable button to prevent double click
            payButton.disabled = true;
            payButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `;

            // Open Midtrans SNAP payment popup
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    console.log('Payment success:', result);
                    @this.call('handlePaymentSuccess', result);
                },
                onPending: function(result) {
                    console.log('Payment pending:', result);
                    @this.call('handlePaymentPending', result);
                },
                onError: function(result) {
                    console.log('Payment error:', result);
                    @this.call('handlePaymentError', result);
                },
                onClose: function() {
                    console.log('Payment popup closed');
                    @this.call('handlePaymentClose');
                }
            });
        });
    });
</script>
@endpush