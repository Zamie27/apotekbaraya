<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verifikasi Email</h1>
            <p class="text-gray-600">Kami telah mengirim kode OTP ke email Anda</p>
            <p class="text-sm text-emerald-600 font-medium">{{ $user->email }}</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-emerald-700 text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-red-700 text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- OTP Form -->
            <form wire:submit.prevent="verifyOTP">
                <div class="mb-6">
                    <label for="otpCode" class="block text-sm font-medium text-gray-700 mb-2">
                        Masukkan Kode OTP (6 digit)
                    </label>
                    <input 
                        type="text" 
                        id="otpCode"
                        wire:model="otpCode"
                        maxlength="6"
                        placeholder="000000"
                        class="w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('otpCode') border-red-500 @enderror"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                    @error('otpCode')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Verify Button -->
                <button 
                    type="submit" 
                    class="w-full bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center"
                    {{ $isLoading ? 'disabled' : '' }}
                >
                    @if ($isLoading)
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memverifikasi...
                    @else
                        Verifikasi Email
                    @endif
                </button>
            </form>

            <!-- Resend Section -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-3">Tidak menerima kode?</p>
                
                @if ($canResend)
                    <button 
                        wire:click="resendOTP"
                        class="text-emerald-600 hover:text-emerald-700 font-medium text-sm transition-colors"
                        {{ $isLoading ? 'disabled' : '' }}
                    >
                        Kirim Ulang Kode OTP
                    </button>
                @else
                    <p class="text-sm text-gray-500">
                        Kirim ulang tersedia dalam 
                        <span class="font-medium text-emerald-600">{{ gmdate('i:s', $resendCountdown) }}</span>
                    </p>
                @endif
            </div>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">Tips:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Kode OTP berlaku selama 10 menit</li>
                            <li>Periksa folder spam jika tidak menerima email</li>
                            <li>Pastikan email yang digunakan aktif</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Butuh bantuan? 
                <a href="mailto:support@apotekbaraya.com" class="text-emerald-600 hover:text-emerald-700 font-medium">
                    Hubungi Support
                </a>
            </p>
        </div>
    </div>

    <!-- Auto-refresh countdown script -->
    @if (!$canResend && $resendCountdown > 0)
        <script>
            let countdown = {{ $resendCountdown }};
            const timer = setInterval(() => {
                countdown--;
                if (countdown <= 0) {
                    clearInterval(timer);
                    @this.set('canResend', true);
                    @this.set('resendCountdown', 0);
                } else {
                    @this.set('resendCountdown', countdown);
                }
            }, 1000);
        </script>
    @endif
</div>
