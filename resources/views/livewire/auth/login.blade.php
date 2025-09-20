<div class="flex items-center justify-center w-full min-h-screen pt-10">
    <fieldset class="p-6 border fieldset bg-base-200 border-base-300 rounded-box w-96">
        <legend class="text-lg font-semibold fieldset-legend">Masuk</legend>

        {{-- Flash Messages --}}
        @if (session('success'))
        <div class="alert alert-success mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('registration_success'))
        <div class="alert alert-info mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <div>
                <div class="font-semibold">Cek Email untuk Aktivasi Akun</div>
                <div class="text-sm">Kami telah mengirimkan link aktivasi ke email Anda. Silakan cek inbox dan klik link tersebut untuk mengaktifkan akun.</div>
            </div>
        </div>
        @endif

        @if (session('email_verified'))
        <div class="alert alert-success mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <div class="font-semibold">Akun Berhasil Diaktivasi!</div>
                <div class="text-sm">Selamat! Akun Anda sudah aktif. Silakan login untuk melanjutkan.</div>
            </div>
        </div>
        @endif

        @if (session('verification_failed'))
        <div class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <div class="font-semibold">Aktivasi Gagal</div>
                <div class="text-sm">Link aktivasi tidak valid atau sudah kedaluwarsa. Silakan minta kirim ulang email aktivasi.</div>
            </div>
        </div>
        @endif

        @if (session('warning'))
        <div class="alert alert-warning mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-error mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form wire:submit="login">
            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input
                    type="email"
                    wire:model="email"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                    placeholder="Masukkan email Anda" />
                @error('email')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Kata Sandi</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        wire:model="password"
                        id="password"
                        class="input input-bordered w-full pr-12 @error('password') input-error @enderror"
                        placeholder="Masukkan kata sandi Anda" />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                        onclick="togglePassword('password')">
                        <x-icons.eye id="password-icon" class="w-5 h-5 text-gray-400" />
                    </button>
                </div>
                @error('password')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="justify-start gap-2 cursor-pointer label">
                    <input
                        type="checkbox"
                        wire:model="remember"
                        class="checkbox checkbox-success" />
                    <span class="label-text">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="w-full mb-4 btn btn-success">
                <span wire:loading.remove>Masuk</span>
                <span wire:loading>
                    <span class="loading loading-spinner loading-sm"></span>
                    Memproses...
                </span>
            </button>
        </form>

        <div class="text-center space-y-2">

            <!-- Forgot Password Link -->
            <p class="text-sm">
                <a href="/forgot-password" class="font-medium text-success hover:underline" wire:navigate>
                    Lupa Password?
                </a>
            </p>
            <!-- Registration Link -->
            <p class="text-sm text-gray-500">
                Belum memiliki akun?
                <a href="/register" class="font-medium text-success hover:underline" wire:navigate>
                    Registrasi di sini
                </a>
            </p>
        </div>
    </fieldset>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                // Replace with eye-off icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
            } else {
                input.type = 'password';
                // Replace with eye icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>

</div>