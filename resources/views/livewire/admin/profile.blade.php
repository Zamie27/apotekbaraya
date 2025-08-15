<div class="max-w-4xl mx-auto p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profil Admin</h1>
        <p class="text-gray-600 mt-2">Kelola informasi profil dan pengaturan akun Anda</p>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information Card -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-6">
                        <x-icons.user class="w-5 h-5" />
                        Informasi Profil
                    </h2>

                    <form wire:submit="updateProfile" class="space-y-4">
                        <!-- Name Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nama Lengkap</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                class="input input-bordered w-full @error('name') input-error @enderror"
                                placeholder="Masukkan nama lengkap"
                            >
                            @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Username Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Username</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="username" 
                                class="input input-bordered w-full @error('username') input-error @enderror"
                                placeholder="Masukkan username"
                            >
                            @error('username')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Email</span>
                            </label>
                            <input 
                                type="email" 
                                wire:model="email" 
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                placeholder="Masukkan email"
                            >
                            @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Phone Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nomor Telepon</span>
                            </label>
                            <input 
                                type="tel" 
                                wire:model="phone" 
                                class="input input-bordered w-full @error('phone') input-error @enderror"
                                placeholder="Masukkan nomor telepon"
                            >
                            @error('phone')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Date of Birth Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Tanggal Lahir</span>
                            </label>
                            <input 
                                type="date" 
                                wire:model="date_of_birth" 
                                class="input input-bordered w-full @error('date_of_birth') input-error @enderror"
                            >
                            @error('date_of_birth')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Gender Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Jenis Kelamin</span>
                            </label>
                            <select wire:model="gender" class="select select-bordered w-full @error('gender') select-error @enderror">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                            @error('gender')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="card-actions justify-end pt-4">
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove>
                                    <x-icons.save class="w-4 h-4 mr-2" />
                                    Simpan Perubahan
                                </span>
                                <span wire:loading>
                                    <span class="loading loading-spinner loading-sm mr-2"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Avatar and Security Card -->
        <div class="space-y-6">
            <!-- Avatar Card -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">
                        <x-icons.camera class="w-5 h-5" />
                        Foto Profil
                    </h3>

                    <!-- Current Avatar Display -->
                    <div class="flex flex-col items-center space-y-4">
                        <div class="avatar">
                            <div class="w-24 h-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                @if($current_avatar)
                                    <img src="{{ Storage::url($current_avatar) }}" alt="Avatar" class="rounded-full">
                                @else
                                    <div class="bg-neutral text-neutral-content rounded-full w-24 h-24 flex items-center justify-center">
                                        <x-icons.user class="w-8 h-8" />
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Avatar Upload -->
                        <div class="form-control w-full">
                            <input 
                                type="file" 
                                wire:model="avatar" 
                                accept="image/*"
                                class="file-input file-input-bordered file-input-sm w-full @error('avatar') file-input-error @enderror"
                            >
                            @error('avatar')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Avatar Actions -->
                        @if($current_avatar)
                        <button 
                            type="button" 
                            wire:click="removeAvatar" 
                            class="btn btn-error btn-sm"
                            wire:confirm="Apakah Anda yakin ingin menghapus foto profil?"
                        >
                            <x-icons.trash class="w-4 h-4 mr-1" />
                            Hapus Foto
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-lg mb-4">
                        <x-icons.shield class="w-5 h-5" />
                        Keamanan
                    </h3>

                    <!-- Toggle Password Form Button -->
                    <button 
                        type="button" 
                        wire:click="togglePasswordForm" 
                        class="btn btn-outline btn-sm w-full"
                    >
                        <x-icons.key class="w-4 h-4 mr-2" />
                        {{ $showPasswordForm ? 'Batal Ubah Password' : 'Ubah Password' }}
                    </button>

                    <!-- Password Change Form -->
                    @if($showPasswordForm)
                    <form wire:submit="updatePassword" class="space-y-4 mt-4">
                        <!-- Current Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Password Saat Ini</span>
                            </label>
                            <input 
                                type="password" 
                                wire:model="current_password" 
                                class="input input-bordered input-sm w-full @error('current_password') input-error @enderror"
                                placeholder="Masukkan password saat ini"
                            >
                            @error('current_password')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Password Baru</span>
                            </label>
                            <input 
                                type="password" 
                                wire:model="new_password" 
                                class="input input-bordered input-sm w-full @error('new_password') input-error @enderror"
                                placeholder="Masukkan password baru"
                            >
                            @error('new_password')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Konfirmasi Password Baru</span>
                            </label>
                            <input 
                                type="password" 
                                wire:model="new_password_confirmation" 
                                class="input input-bordered input-sm w-full"
                                placeholder="Konfirmasi password baru"
                            >
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-sm w-full">
                            <span wire:loading.remove>
                                <x-icons.check class="w-4 h-4 mr-2" />
                                Update Password
                            </span>
                            <span wire:loading>
                                <span class="loading loading-spinner loading-sm mr-2"></span>
                                Memperbarui...
                            </span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>