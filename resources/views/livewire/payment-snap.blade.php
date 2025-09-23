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
                <button id="pay-button" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <!-- Spinner (hidden by default) -->
                    <svg id="button-spinner" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <!-- Payment icon (visible by default) -->
                    <svg id="button-icon" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <!-- Button text -->
                    <span id="button-text">Bayar Sekarang</span>
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
    // Global error handler for debugging
    window.addEventListener('error', function(event) {
        console.error('Global JavaScript error detected:', {
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            error: event.error
        });
        
        // If error occurs during redirect, try emergency redirect
        if (window.redirectInProgress && event.message.includes('redirect')) {
            console.log('Error during redirect detected, attempting emergency redirect...');
            setTimeout(function() {
                window.location.replace("{{ route('pelanggan.orders.show', $order->order_id) }}");
            }, 500);
        }
    });
    
    // Unhandled promise rejection handler
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
        
        // If promise rejection occurs during redirect, try emergency redirect
        if (window.redirectInProgress) {
            console.log('Promise rejection during redirect, attempting emergency redirect...');
            setTimeout(function() {
                window.location.replace("{{ route('pelanggan.orders.show', $order->order_id) }}");
            }, 500);
        }
    });
    
    // Monitor for navigation events
    window.addEventListener('beforeunload', function(event) {
        console.log('Page is about to unload - redirect successful');
        window.redirectCompleted = true;
    });
    
    // Monitor for page visibility changes (might indicate redirect issues)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && window.redirectInProgress) {
            console.log('Page became hidden during redirect - this might indicate redirect success');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Payment Snap page loaded - DOM ready');
        
        // Check if required elements exist
        const requiredElements = ['pay-button', 'button-text', 'button-spinner', 'button-icon'];
        requiredElements.forEach(id => {
            const element = document.getElementById(id);
            if (!element) {
                console.error(`Required element not found: ${id}`);
            } else {
                console.log(`Element found: ${id}`);
            }
        });
        
        // Function to show processing state
        function showProcessingState() {
            console.log('Showing processing state...');
            const payButton = document.getElementById('pay-button');
            const buttonText = document.getElementById('button-text');
            const buttonSpinner = document.getElementById('button-spinner');
            const buttonIcon = document.getElementById('button-icon');
            
            if (payButton && buttonText && buttonSpinner && buttonIcon) {
                payButton.disabled = true;
                buttonText.textContent = 'Memproses...';
                buttonSpinner.classList.remove('hidden');
                buttonIcon.classList.add('hidden');
                console.log('Processing state applied successfully');
            } else {
                console.error('Failed to apply processing state - missing elements');
            }
        }
        
        // Check if Midtrans SNAP is loaded
        if (typeof window.snap === 'undefined') {
            console.error('Midtrans SNAP not loaded!');
            console.log('Available window properties:', Object.keys(window));
            return;
        }
        
        if (typeof window.snap.pay !== 'function') {
            console.error('Midtrans SNAP pay method not available!');
            console.log('SNAP object:', window.snap);
            return;
        }
        
        console.log('Midtrans SNAP is available and functional');
        console.log('SNAP object methods:', Object.keys(window.snap));
        
        // Check if snap token is available
        const snapToken = '{{ $snapToken }}';
        console.log('Snap token from backend:', snapToken);
        
        if (!snapToken || snapToken === '') {
            console.error('Snap token is empty or not available!');
            alert('Token pembayaran tidak tersedia. Silakan coba lagi.');
            return;
        }

        // Handle pay button click
        document.getElementById('pay-button').addEventListener('click', function() {
            console.log('Pay button clicked');
            console.log('Using snap token:', snapToken);
            
            // Show processing state
            showProcessingState();
            
            // Set a flag to track popup state
            window.popupOpened = true;
            window.popupClosed = false;
            window.popupElementsDetected = false;
            
            // Fallback timer in case popup doesn't trigger onClose (very conservative)
            window.fallbackTimer = setTimeout(function() {
                if (window.popupOpened && !window.popupClosed && !window.redirectInProgress) {
                    console.log('Fallback: Popup seems to be closed but onClose was not triggered');
                    console.log('Attempting fallback redirect...');
                    window.redirectInProgress = true;
                    window.location.href = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                }
            }, 300000); // 5 minutes fallback (very conservative)
            
            // Alternative: Polling method to detect popup closure (with delay to allow popup to load)
            setTimeout(function() {
                let pollCount = 0;
                const maxPolls = 300; // Maximum 5 minutes of polling (300 * 1 second)
                
                window.popupPolling = setInterval(function() {
                    pollCount++;
                    
                    // Stop polling after maximum attempts
                    if (pollCount > maxPolls) {
                        console.log('Polling stopped: Maximum attempts reached');
                        clearInterval(window.popupPolling);
                        return;
                    }
                    
                    // Check if popup elements are still visible
                    const snapOverlay = document.querySelector('.snap-overlay');
                    const snapContainer = document.querySelector('.snap-container');
                    const midtransModal = document.querySelector('[id*="snap"]');
                    
                    // Detect if popup elements are present (mark as detected)
                    if ((snapOverlay || snapContainer || midtransModal) && !window.popupElementsDetected) {
                        window.popupElementsDetected = true;
                        console.log('Polling detected: Midtrans popup elements are now present');
                    }
                    
                    if (window.popupOpened && !window.popupClosed) {
                        // Only trigger if we previously detected popup elements and now they're gone
                        // AND we've been polling for at least 5 seconds (to avoid false positives)
                        if (window.popupElementsDetected && !snapOverlay && !snapContainer && !midtransModal && pollCount > 5) {
                            console.log('Polling detected: Popup elements removed after being present, popup closed');
                            window.popupClosed = true;
                            clearInterval(window.popupPolling);
                            
                            if (!window.redirectInProgress) {
                                console.log('Polling triggered redirect...');
                                window.redirectInProgress = true;
                                
                                // Call Livewire method for logging
                                @this.call('handlePaymentClose');
                                
                                // Update button state
                                showProcessingState();
                                
                                // Redirect after short delay
                                setTimeout(function() {
                                    window.location.href = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                                }, 100);
                            }
                        }
                    }
                }, 1000); // Check every 1 second
            }, 3000); // Wait 3 seconds before starting polling to allow popup to load
             
            // Open Midtrans SNAP payment popup
            console.log('Attempting to open Midtrans SNAP popup...');
            console.log('Snap Token:', snapToken);
            console.log('Current timestamp:', new Date().toISOString());
            
            // Mark popup as opened immediately when snap.pay is called
            window.popupOpened = true;
            console.log('Popup marked as opened');
            
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('=== MIDTRANS CALLBACK: onSuccess ===');
                    console.log('Payment success:', result);
                    console.log('Timestamp:', new Date().toISOString());
                    
                    // Prevent multiple redirects
                    if (window.redirectInProgress) {
                        console.log('Redirect already in progress, skipping onSuccess...');
                        return;
                    }
                    window.redirectInProgress = true;
                    
                    // Mark popup as closed to prevent other callbacks
                    window.popupClosed = true;
                    
                    // Clear all timers and observers
                    if (window.fallbackTimer) {
                        clearTimeout(window.fallbackTimer);
                        console.log('Fallback timer cleared from onSuccess');
                    }
                    if (window.popupPolling) {
                        clearInterval(window.popupPolling);
                        console.log('Popup polling cleared from onSuccess');
                    }
                    if (window.popupObserver) {
                        window.popupObserver.disconnect();
                        console.log('Popup observer disconnected from onSuccess');
                    }
                    
                    // Update button state immediately
                    try {
                        const payButton = document.getElementById('pay-button');
                        const buttonText = document.getElementById('button-text');
                        const buttonSpinner = document.getElementById('button-spinner');
                        const buttonIcon = document.getElementById('button-icon');
                        
                        if (payButton && buttonText) {
                            payButton.disabled = true;
                            buttonText.textContent = 'Pembayaran Berhasil!';
                            if (buttonSpinner) buttonSpinner.classList.add('hidden');
                            if (buttonIcon) buttonIcon.classList.add('hidden');
                            console.log('Button state updated to success');
                        }
                    } catch (e) {
                        console.warn('Failed to update button state in onSuccess:', e);
                    }
                    
                    // Call Livewire method with error handling
                    try {
                        if (window.Livewire && window.Livewire.find('{{ $this->getId() }}')) {
                            window.Livewire.find('{{ $this->getId() }}').call('handlePaymentSuccess', result);
                            console.log('Livewire handlePaymentSuccess called successfully');
                        } else {
                            console.warn('Livewire component not available, skipping backend call');
                        }
                    } catch (e) {
                        console.error('Error calling Livewire handlePaymentSuccess:', e);
                    }
                    
                    // Redirect to order detail page
                    const redirectUrl = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                    console.log('Redirecting to order detail:', redirectUrl);
                    
                    setTimeout(function() {
                        console.log('Executing success redirect...');
                        window.location.href = redirectUrl;
                    }, 1500); // Give user time to see success message
                },
                onPending: function(result) {
                    console.log('=== MIDTRANS CALLBACK: onPending ===');
                    console.log('Payment pending:', result);
                    console.log('Timestamp:', new Date().toISOString());
                    
                    // Prevent multiple redirects
                    if (window.redirectInProgress) {
                        console.log('Redirect already in progress, skipping onPending...');
                        return;
                    }
                    window.redirectInProgress = true;
                    
                    // Mark popup as closed to prevent other callbacks
                    window.popupClosed = true;
                    
                    // Clear all timers and observers
                    if (window.fallbackTimer) {
                        clearTimeout(window.fallbackTimer);
                        console.log('Fallback timer cleared from onPending');
                    }
                    if (window.popupPolling) {
                        clearInterval(window.popupPolling);
                        console.log('Popup polling cleared from onPending');
                    }
                    if (window.popupObserver) {
                        window.popupObserver.disconnect();
                        console.log('Popup observer disconnected from onPending');
                    }
                    
                    // Update button state immediately
                    try {
                        const payButton = document.getElementById('pay-button');
                        const buttonText = document.getElementById('button-text');
                        const buttonSpinner = document.getElementById('button-spinner');
                        const buttonIcon = document.getElementById('button-icon');
                        
                        if (payButton && buttonText) {
                            payButton.disabled = true;
                            buttonText.textContent = 'Pembayaran Pending...';
                            if (buttonSpinner) buttonSpinner.classList.remove('hidden');
                            if (buttonIcon) buttonIcon.classList.add('hidden');
                            console.log('Button state updated to pending');
                        }
                    } catch (e) {
                        console.warn('Failed to update button state in onPending:', e);
                    }
                    
                    // Call Livewire method with error handling
                    try {
                        if (window.Livewire && window.Livewire.find('{{ $this->getId() }}')) {
                            window.Livewire.find('{{ $this->getId() }}').call('handlePaymentPending', result);
                            console.log('Livewire handlePaymentPending called successfully');
                        } else {
                            console.warn('Livewire component not available, skipping backend call');
                        }
                    } catch (e) {
                        console.error('Error calling Livewire handlePaymentPending:', e);
                    }
                    
                    // Redirect to order detail page
                    const redirectUrl = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                    console.log('Redirecting to order detail:', redirectUrl);
                    
                    setTimeout(function() {
                        console.log('Executing pending redirect...');
                        window.location.href = redirectUrl;
                    }, 1500); // Give user time to see pending message
                },
                onError: function(result) {
                    console.log('=== MIDTRANS CALLBACK: onError ===');
                    console.log('Payment error:', result);
                    console.log('Timestamp:', new Date().toISOString());
                    
                    // Prevent multiple redirects
                    if (window.redirectInProgress) {
                        console.log('Redirect already in progress, skipping onError...');
                        return;
                    }
                    window.redirectInProgress = true;
                    
                    // Mark popup as closed to prevent other callbacks
                    window.popupClosed = true;
                    
                    // Clear all timers and observers
                    if (window.fallbackTimer) {
                        clearTimeout(window.fallbackTimer);
                        console.log('Fallback timer cleared from onError');
                    }
                    if (window.popupPolling) {
                        clearInterval(window.popupPolling);
                        console.log('Popup polling cleared from onError');
                    }
                    if (window.popupObserver) {
                        window.popupObserver.disconnect();
                        console.log('Popup observer disconnected from onError');
                    }
                    
                    // Update button state immediately
                    try {
                        const payButton = document.getElementById('pay-button');
                        const buttonText = document.getElementById('button-text');
                        const buttonSpinner = document.getElementById('button-spinner');
                        const buttonIcon = document.getElementById('button-icon');
                        
                        if (payButton && buttonText) {
                            payButton.disabled = false; // Re-enable button for retry
                            buttonText.textContent = 'Bayar Sekarang';
                            if (buttonSpinner) buttonSpinner.classList.add('hidden');
                            if (buttonIcon) buttonIcon.classList.remove('hidden');
                            console.log('Button state reset for retry');
                        }
                    } catch (e) {
                        console.warn('Failed to update button state in onError:', e);
                    }
                    
                    // Call Livewire method with error handling
                    try {
                        if (window.Livewire && window.Livewire.find('{{ $this->getId() }}')) {
                            window.Livewire.find('{{ $this->getId() }}').call('handlePaymentError', result);
                            console.log('Livewire handlePaymentError called successfully');
                        } else {
                            console.warn('Livewire component not available, skipping backend call');
                        }
                    } catch (e) {
                        console.error('Error calling Livewire handlePaymentError:', e);
                    }
                    
                    // For error case, redirect back to order detail after showing error
                    const redirectUrl = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                    console.log('Redirecting to order detail after error:', redirectUrl);
                    
                    setTimeout(function() {
                        console.log('Executing error redirect...');
                        window.location.href = redirectUrl;
                    }, 2000); // Give user more time to see error message
                },
                onClose: function() {
                    console.log('=== MIDTRANS POPUP CLOSED ===');
                    console.log('Timestamp:', new Date().toISOString());
                    
                    // Mark popup as closed and clear timers
                    window.popupClosed = true;
                    if (window.fallbackTimer) {
                        clearTimeout(window.fallbackTimer);
                        console.log('Fallback timer cleared');
                    }
                    if (window.popupPolling) {
                        clearInterval(window.popupPolling);
                        console.log('Popup polling cleared');
                    }
                    if (window.popupObserver) {
                        window.popupObserver.disconnect();
                        console.log('Popup observer disconnected');
                    }
                    
                    // Prevent multiple redirects
                    if (window.redirectInProgress) {
                        console.log('Redirect already in progress, skipping...');
                        return;
                    }
                    window.redirectInProgress = true;
                    
                    const redirectUrl = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                    console.log('Target redirect URL:', redirectUrl);
                    
                    // Update UI immediately
                    try {
                        const payButton = document.getElementById('pay-button');
                        const buttonText = document.getElementById('button-text');
                        const buttonSpinner = document.getElementById('button-spinner');
                        const buttonIcon = document.getElementById('button-icon');
                        
                        if (payButton && buttonText) {
                            payButton.disabled = true;
                            buttonText.textContent = 'Mengalihkan...';
                            if (buttonSpinner) buttonSpinner.classList.remove('hidden');
                            if (buttonIcon) buttonIcon.classList.add('hidden');
                            console.log('Button state updated');
                        }
                    } catch (e) {
                        console.warn('Failed to update button state:', e);
                    }
                    
                    // Call Livewire method for logging (non-blocking)
                    try {
                        if (window.Livewire && window.Livewire.find('{{ $this->getId() }}')) {
                            window.Livewire.find('{{ $this->getId() }}').call('handlePaymentClose');
                            console.log('Livewire handlePaymentClose called successfully');
                        } else {
                            console.warn('Livewire component not available, skipping backend call');
                        }
                    } catch (e) {
                        console.error('Error calling Livewire handlePaymentClose:', e);
                    }
                    
                    // Simple, reliable redirect with minimal delay
                    console.log('Executing redirect in 100ms...');
                    setTimeout(function() {
                        console.log('Redirecting now...');
                        window.location.href = redirectUrl;
                    }, 100);
                }
            });
            
            // Initialize MutationObserver after snap.pay() is called (with longer delay)
            setTimeout(function() {
                console.log('Initializing MutationObserver...');
                window.popupObserver = new MutationObserver(function(mutations) {
                    if (window.popupOpened && !window.popupClosed) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'childList') {
                                // First, check if Midtrans elements are being added
                                mutation.addedNodes.forEach(function(node) {
                                    if (node.nodeType === Node.ELEMENT_NODE) {
                                        if (node.classList && (
                                            node.classList.contains('snap-overlay') ||
                                            node.classList.contains('snap-container') ||
                                            node.id && node.id.includes('snap')
                                        )) {
                                            window.popupElementsDetected = true;
                                            console.log('MutationObserver detected: Midtrans popup elements added');
                                        }
                                    }
                                });
                                
                                // Only check for removal if elements were previously detected
                                if (window.popupElementsDetected) {
                                    mutation.removedNodes.forEach(function(node) {
                                        if (node.nodeType === Node.ELEMENT_NODE) {
                                            if (node.classList && (
                                                node.classList.contains('snap-overlay') ||
                                                node.classList.contains('snap-container') ||
                                                node.id && node.id.includes('snap')
                                            )) {
                                                console.log('MutationObserver detected: Midtrans popup element removed');
                                                window.popupClosed = true;
                                                window.popupObserver.disconnect();
                                                
                                                if (!window.redirectInProgress) {
                                                    console.log('MutationObserver triggered redirect...');
                                                    window.redirectInProgress = true;
                                                    
                                                    // Call Livewire method for logging
                                                    @this.call('handlePaymentClose');
                                                    
                                                    // Update button state
                                                    showProcessingState();
                                                    
                                                    // Redirect after short delay
                                                    setTimeout(function() {
                                                        window.location.href = "{{ route('pelanggan.orders.show', $order->order_id) }}";
                                                    }, 100);
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
                
                // Start observing DOM changes
                window.popupObserver.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }, 5000); // Wait 5 seconds after snap.pay() is called to ensure popup is fully loaded
        });
    });

    // Mark redirect as completed when page starts to unload
    window.addEventListener('beforeunload', function() {
        console.log('Page unloading - marking redirect as completed');
        window.redirectCompleted = true;
    });
    
    // Listen for Livewire events (backup method)
    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized');
        
        // Validate required DOM elements
        const payButton = document.getElementById('pay-button');
        const buttonText = document.getElementById('button-text');
        const buttonSpinner = document.getElementById('button-spinner');
        const buttonIcon = document.getElementById('button-icon');
        
        if (!payButton) {
            console.error('Pay button not found!');
            return;
        }
        
        if (!buttonText) {
            console.error('Button text element not found!');
            return;
        }
        
        if (!buttonSpinner) {
            console.error('Button spinner element not found!');
            return;
        }
        
        if (!buttonIcon) {
            console.error('Button icon element not found!');
            return;
        }
        
        console.log('All required DOM elements found');
        
        // Prevent multiple redirect attempts
        if (window.redirectInProgress) {
            console.log('Redirect already in progress, skipping initialization...');
            return;
        }
        
        console.log('Livewire initialized - ready for payment processing');
    });
</script>
@endpush