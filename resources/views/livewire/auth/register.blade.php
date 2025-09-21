<div class="flex items-center justify-center w-full min-h-screen pt-10">
    <fieldset class="p-6 border fieldset bg-base-200 border-base-300 rounded-box w-96">
        <legend class="text-lg font-semibold fieldset-legend">Registrasi</legend>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
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

        <form wire:submit="register">
            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Nama Lengkap</span>
                </label>
                <input
                    type="text"
                    wire:model="name"
                    class="input input-bordered w-full @error('name') input-error @enderror"
                    placeholder="Masukkan nama lengkap" />
                @error('name')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Username</span>
                </label>
                <input
                    type="text"
                    wire:model="username"
                    class="input input-bordered w-full @error('username') input-error @enderror"
                    placeholder="Masukkan username" />
                @error('username')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input
                    type="email"
                    wire:model="email"
                    class="input input-bordered w-full @error('email') input-error @enderror"
                    placeholder="Masukkan email" />
                @error('email')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Nomor Telepon</span>
                </label>
                <input
                    type="tel"
                    wire:model="phone"
                    class="input input-bordered w-full @error('phone') input-error @enderror"
                    placeholder="Contoh: 081234567890" />
                @error('phone')
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
                        placeholder="Minimal 8 karakter dengan huruf kapital, angka, dan simbol"
                        oninput="validatePassword(this.value)" />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                        onclick="togglePassword('password')">
                        <x-icons.eye id="password-icon" class="w-5 h-5 text-gray-400" />
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div id="password-strength" class="mt-2 hidden">
                    <div class="text-xs font-medium mb-1">Kekuatan Password:</div>
                    <div class="flex gap-1 mb-2">
                        <div id="strength-bar-1" class="h-1 w-1/4 bg-gray-300 rounded"></div>
                        <div id="strength-bar-2" class="h-1 w-1/4 bg-gray-300 rounded"></div>
                        <div id="strength-bar-3" class="h-1 w-1/4 bg-gray-300 rounded"></div>
                        <div id="strength-bar-4" class="h-1 w-1/4 bg-gray-300 rounded"></div>
                    </div>
                    <div id="strength-text" class="text-xs text-gray-500"></div>
                </div>

                <!-- Password Requirements -->
                <div class="mt-2 text-xs space-y-1">
                    <div id="req-length" class="flex items-center gap-2 text-gray-500">
                        <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                            <span class="hidden text-green-500 text-xs">✓</span>
                        </span>
                        Minimal 8 karakter
                    </div>
                    <div id="req-uppercase" class="flex items-center gap-2 text-gray-500">
                        <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                            <span class="hidden text-green-500 text-xs">✓</span>
                        </span>
                        Minimal 1 huruf kapital (A-Z)
                    </div>
                    <div id="req-lowercase" class="flex items-center gap-2 text-gray-500">
                        <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                            <span class="hidden text-green-500 text-xs">✓</span>
                        </span>
                        Minimal 1 huruf kecil (a-z)
                    </div>
                    <div id="req-number" class="flex items-center gap-2 text-gray-500">
                        <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                            <span class="hidden text-green-500 text-xs">✓</span>
                        </span>
                        Minimal 1 angka (0-9)
                    </div>
                    <div id="req-symbol" class="flex items-center gap-2 text-gray-500">
                        <span class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                            <span class="hidden text-green-500 text-xs">✓</span>
                        </span>
                        Minimal 1 simbol (@#$*!)
                    </div>
                </div>

                @error('password')
                <span class="text-sm text-error mt-2 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="label">
                    <span class="label-text">Konfirmasi Kata Sandi</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        wire:model="password_confirmation"
                        id="password_confirmation"
                        class="input input-bordered w-full pr-12 @error('password_confirmation') input-error @enderror"
                        placeholder="Ulangi kata sandi" />
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-3"
                        onclick="togglePassword('password_confirmation')">
                        <x-icons.eye id="password_confirmation-icon" class="w-5 h-5 text-gray-400" />
                    </button>
                </div>
                @error('password_confirmation')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="justify-start gap-2 cursor-pointer label">
                    <input
                        type="checkbox"
                        wire:model="terms"
                        class="checkbox @error('terms') checkbox-error @enderror" />
                    <span class="text-sm label-text">
                        Saya menerima
                        <a href="#" class="font-medium text-success hover:underline">
                            Syarat dan Ketentuan
                        </a>
                    </span>
                </label>
                @error('terms')
                <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full mb-4 btn btn-success">
                <span wire:loading.remove>Buat Akun</span>
                <span wire:loading>
                    <span class="loading loading-spinner loading-sm"></span>
                    Memproses...
                </span>
            </button>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-500">
                Sudah punya akun?
                <a href="/login" class="font-medium text-success hover:underline" wire:navigate>
                    Masuk di sini
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

        function validatePassword(password) {
            const strengthIndicator = document.getElementById('password-strength');
            const strengthBars = [
                document.getElementById('strength-bar-1'),
                document.getElementById('strength-bar-2'),
                document.getElementById('strength-bar-3'),
                document.getElementById('strength-bar-4')
            ];
            const strengthText = document.getElementById('strength-text');

            // Requirements elements
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqNumber = document.getElementById('req-number');
            const reqSymbol = document.getElementById('req-symbol');

            // Show strength indicator if password is not empty
            if (password.length > 0) {
                strengthIndicator.classList.remove('hidden');
            } else {
                strengthIndicator.classList.add('hidden');
                resetRequirements();
                return;
            }

            // Check requirements
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasSymbol = /[@#$*!]/.test(password);

            // Update requirement indicators
            updateRequirement(reqLength, hasLength);
            updateRequirement(reqUppercase, hasUppercase);
            updateRequirement(reqLowercase, hasLowercase);
            updateRequirement(reqNumber, hasNumber);
            updateRequirement(reqSymbol, hasSymbol);

            // Calculate strength score
            let score = 0;
            if (hasLength) score++;
            if (hasUppercase) score++;
            if (hasLowercase) score++;
            if (hasNumber) score++;
            if (hasSymbol) score++;

            // Reset all bars
            strengthBars.forEach(bar => {
                bar.className = 'h-1 w-1/4 bg-gray-300 rounded';
            });

            // Update strength bars and text
            if (score === 0) {
                strengthText.textContent = 'Sangat Lemah';
                strengthText.className = 'text-xs text-red-500';
            } else if (score === 1) {
                strengthBars[0].className = 'h-1 w-1/4 bg-red-500 rounded';
                strengthText.textContent = 'Lemah';
                strengthText.className = 'text-xs text-red-500';
            } else if (score === 2) {
                strengthBars[0].className = 'h-1 w-1/4 bg-red-500 rounded';
                strengthBars[1].className = 'h-1 w-1/4 bg-orange-500 rounded';
                strengthText.textContent = 'Sedang';
                strengthText.className = 'text-xs text-orange-500';
            } else if (score === 3) {
                strengthBars[0].className = 'h-1 w-1/4 bg-orange-500 rounded';
                strengthBars[1].className = 'h-1 w-1/4 bg-orange-500 rounded';
                strengthBars[2].className = 'h-1 w-1/4 bg-yellow-500 rounded';
                strengthText.textContent = 'Baik';
                strengthText.className = 'text-xs text-yellow-600';
            } else if (score === 4) {
                strengthBars[0].className = 'h-1 w-1/4 bg-yellow-500 rounded';
                strengthBars[1].className = 'h-1 w-1/4 bg-yellow-500 rounded';
                strengthBars[2].className = 'h-1 w-1/4 bg-yellow-500 rounded';
                strengthBars[3].className = 'h-1 w-1/4 bg-green-500 rounded';
                strengthText.textContent = 'Kuat';
                strengthText.className = 'text-xs text-green-600';
            } else if (score === 5) {
                strengthBars.forEach(bar => {
                    bar.className = 'h-1 w-1/4 bg-green-500 rounded';
                });
                strengthText.textContent = 'Sangat Kuat';
                strengthText.className = 'text-xs text-green-600';
            }
        }

        function updateRequirement(element, isMet) {
            const circle = element.querySelector('span:first-child');
            const checkmark = element.querySelector('span:last-child');

            if (isMet) {
                circle.className = 'w-4 h-4 rounded-full bg-green-500 flex items-center justify-center';
                checkmark.className = 'text-white text-xs';
                element.className = 'flex items-center gap-2 text-green-600';
            } else {
                circle.className = 'w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center';
                checkmark.className = 'hidden text-green-500 text-xs';
                element.className = 'flex items-center gap-2 text-gray-500';
            }
        }

        function resetRequirements() {
            const requirements = ['req-length', 'req-uppercase', 'req-lowercase', 'req-number', 'req-symbol'];
            requirements.forEach(reqId => {
                const element = document.getElementById(reqId);
                updateRequirement(element, false);
            });
        }
    </script>

</div>
