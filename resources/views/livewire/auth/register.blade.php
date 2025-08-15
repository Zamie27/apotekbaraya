<div class="flex items-center justify-center w-full min-h-screen pt-10">
    <fieldset class="p-6 border fieldset bg-base-200 border-base-300 rounded-box w-96">
        <legend class="text-lg font-semibold fieldset-legend">Registrasi</legend>

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
                        placeholder="Minimal 6 karakter" />
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
    </script>

</div>
