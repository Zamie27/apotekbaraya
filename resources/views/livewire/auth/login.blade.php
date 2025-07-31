<div class="flex items-center justify-center w-full min-h-screen pt-10">
    <fieldset class="p-6 border fieldset bg-base-200 border-base-300 rounded-box w-96">
        <legend class="text-lg font-semibold fieldset-legend">Masuk</legend>

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
                        <i id="password-icon" data-feather="eye" class="w-5 h-5 text-gray-400"></i>
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

        <div class="text-center">
            <p class="text-sm text-gray-500">
                Belum memiliki akun?
                <a href="/register" class="font-medium text-success hover:underline" wire:navigate>
                    Registrasi di sini
                </a>
            </p>
        </div>

        <!-- Test Accounts Info -->
        <div class="p-4 mt-6 rounded-lg bg-info/10">
            <h3 class="mb-2 text-sm font-medium">Akun Test:</h3>
            <div class="space-y-1 text-xs">
                <p><strong>Admin:</strong> admin@apotekbaraya.com | password</p>
                <p><strong>Apoteker:</strong> apoteker@apotekbaraya.com | password</p>
                <p><strong>Kurir:</strong> kurir@apotekbaraya.com | password</p>
                <p><strong>Pelanggan:</strong> pelanggan@apotekbaraya.com | password</p>
            </div>
        </div>
    </fieldset>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-feather', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-feather', 'eye');
            }

            // Re-render feather icons
            feather.replace();
        }
    </script>

</div>
