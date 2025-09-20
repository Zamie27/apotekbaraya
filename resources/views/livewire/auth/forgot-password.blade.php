<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-emerald-50 to-teal-100 px-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-primary rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Lupa Password</h1>
            <p class="text-gray-600 mt-2">Masukkan email Anda untuk menerima link reset password</p>
        </div>

        <!-- Card -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                @if($emailSent)
                    <!-- Success Message -->
                    <div class="text-center space-y-4">
                        <div class="mx-auto w-16 h-16 bg-success rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Email Terkirim!</h3>
                            <p class="text-gray-600 text-sm mt-2">
                                Kami telah mengirim link reset password ke <strong>{{ $email }}</strong>. 
                                Silakan cek inbox atau folder spam Anda.
                            </p>
                        </div>
                        <div class="space-y-2">
                            <button wire:click="$set('emailSent', false)" class="btn btn-outline btn-sm">
                                Kirim Ulang
                            </button>
                            <div>
                                <a href="/login" class="link link-primary text-sm" wire:navigate>
                                    Kembali ke Login
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Forgot Password Form -->
                    <form wire:submit="sendResetLink" class="space-y-4">
                        <!-- Email Input -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Alamat Email</span>
                            </label>
                            <input
                                type="email"
                                wire:model="email"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                placeholder="Masukkan alamat email Anda"
                                required
                            >
                            @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full btn btn-primary">
                            <span wire:loading.remove>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Kirim Link Reset
                            </span>
                            <span wire:loading>
                                <span class="loading loading-spinner loading-sm mr-2"></span>
                                Mengirim...
                            </span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="text-center mt-4">
                        <a href="/login" class="link link-primary text-sm" wire:navigate>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Login
                        </a>
                    </div>
                @endif

                <!-- Flash Messages -->
                @if (session()->has('success'))
                <div class="alert alert-success mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if (session()->has('error'))
                <div class="alert alert-error mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
