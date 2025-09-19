<div class="max-w-4xl mx-auto px-2 sm:px-4 lg:px-6 py-4 sm:py-6">
    <!-- Page Header -->
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">Kelola informasi profil dan pengaturan akun Anda</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Profile Information Card -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body p-4 sm:p-6">
                    <h2 class="card-title text-lg sm:text-xl mb-4 sm:mb-6">
                        <x-icons.user class="w-4 h-4 sm:w-5 sm:h-5" />
                        Informasi Profil
                    </h2>

                    <form wire:submit="updateProfile" class="space-y-3 sm:space-y-4">
                        <!-- Name Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm sm:text-base font-medium">Nama Lengkap</span>
                            </label>
                            <input
                                type="text"
                                wire:model="name"
                                class="input input-bordered w-full text-sm sm:text-base @error('name') input-error @enderror"
                                placeholder="Masukkan nama lengkap">
                            @error('name')
                            <label class="label">
                                <span class="label-text-alt text-xs sm:text-sm text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Username Field (Disabled) -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm sm:text-base font-medium">Username</span>
                                <span class="label-text-alt text-xs text-gray-500">Tidak dapat diubah</span>
                            </label>
                            <input
                                type="text"
                                wire:model="username"
                                disabled
                                class="input input-bordered w-full text-sm sm:text-base bg-gray-100 cursor-not-allowed"
                                placeholder="Username tidak dapat diubah">
                            <label class="label">
                                <span class="label-text-alt text-xs text-gray-500">Username bersifat permanen dan tidak dapat diubah</span>
                            </label>
                        </div>

                        <!-- Email Field (Disabled) -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm sm:text-base font-medium">Email</span>
                                <span class="label-text-alt text-xs text-gray-500">Tidak dapat diubah</span>
                            </label>
                            <input
                                type="email"
                                wire:model="email"
                                disabled
                                class="input input-bordered w-full text-sm sm:text-base bg-gray-100 cursor-not-allowed"
                                placeholder="Email tidak dapat diubah">
                            <label class="label">
                                <span class="label-text-alt text-xs text-gray-500">Email terkait dengan verifikasi akun dan tidak dapat diubah</span>
                            </label>
                        </div>

                        <!-- Phone Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm sm:text-base font-medium">Nomor Telepon</span>
                            </label>
                            <input
                                type="tel"
                                wire:model="phone"
                                class="input input-bordered w-full text-sm sm:text-base @error('phone') input-error @enderror"
                                placeholder="Masukkan nomor telepon">
                            @error('phone')
                            <label class="label">
                                <span class="label-text-alt text-xs sm:text-sm text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Date of Birth Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm sm:text-base font-medium">Tanggal Lahir</span>
                            </label>
                            <input
                                type="date"
                                wire:model="date_of_birth"
                                class="input input-bordered w-full text-sm sm:text-base @error('date_of_birth') input-error @enderror">
                            @error('date_of_birth')
                            <label class="label">
                                <span class="label-text-alt text-xs sm:text-sm text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Gender Field -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text text-sm sm:text-base font-medium">Jenis Kelamin</span>
                            </label>
                            <select wire:model="gender" class="select select-bordered w-full text-sm sm:text-base @error('gender') select-error @enderror">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                            @error('gender')
                            <label class="label">
                                <span class="label-text-alt text-xs sm:text-sm text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="card-actions justify-end pt-4">
                            <button type="submit" class="btn btn-success btn-sm sm:btn-md">
                                <span wire:loading.remove>
                                    <span class="text-xs sm:text-sm">Simpan Perubahan</span>
                                </span>
                                <span wire:loading>
                                    <span class="loading loading-spinner loading-xs sm:loading-sm mr-1 sm:mr-2"></span>
                                    <span class="text-xs sm:text-sm">Menyimpan...</span>
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
                <div class="card-body p-4 sm:p-6">
                    <h3 class="card-title text-base sm:text-lg mb-3 sm:mb-4">
                        <x-icons.camera class="w-4 h-4 sm:w-5 sm:h-5" />
                        Foto Profil
                    </h3>

                    <!-- Current Avatar Display -->
                    <div class="flex flex-col items-center space-y-3 sm:space-y-4">
                        <div class="avatar">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full ring ring-success ring-offset-base-100 ring-offset-2">
                                @if($current_avatar)
                                <img src="{{ Storage::url($current_avatar) }}" alt="Avatar" class="rounded-full">
                                @else
                                <div class="bg-neutral text-neutral-content rounded-full w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center">
                                    <x-icons.user class="w-6 h-6 sm:w-8 sm:h-8" />
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
                                class="file-input file-input-bordered file-input-xs sm:file-input-sm w-full text-xs sm:text-sm @error('avatar') file-input-error @enderror">
                            @error('avatar')
                            <label class="label">
                                <span class="label-text-alt text-xs sm:text-sm text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Avatar Actions -->
                        @if($current_avatar)
                        <button
                            type="button"
                            wire:click="removeAvatar"
                            class="btn btn-error btn-xs sm:btn-sm"
                            wire:confirm="Apakah Anda yakin ingin menghapus foto profil?">
                            <x-icons.trash class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
                            <span class="text-xs sm:text-sm">Hapus Foto</span>
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
                        class="btn btn-outline btn-sm w-full">
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
                                placeholder="Masukkan password saat ini">
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
                                placeholder="Masukkan password baru">
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
                                placeholder="Konfirmasi password baru">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-success btn-sm w-full">
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

    <!-- Address Management Section -->
    <div class="mt-6 sm:mt-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 mb-4 sm:mb-6">
                    <h2 class="card-title text-lg sm:text-xl">
                        <x-icons.map-pin class="w-4 h-4 sm:w-5 sm:h-5" />
                        Alamat Saya
                    </h2>
                    <button wire:click="openAddressForm" class="btn btn-success btn-sm w-full sm:w-auto">
                        <x-icons.plus class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" />
                        <span class="text-xs sm:text-sm">Tambah Alamat</span>
                    </button>
                </div>

                <!-- Shipping Area Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg alert alert-info mb-4 sm:mb-6 p-3 sm:p-4">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-blue-800 mb-2 text-sm sm:text-base">Layanan pengiriman tersedia untuk alamat dengan jarak maksimal:</h3>
                            <div class="flex items-center gap-2 sm:gap-3 mb-2 sm:mb-3">
                                <div class="flex items-center gap-1 sm:gap-2">
                                    <span class="w-2 h-2 sm:w-3 sm:h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-base sm:text-lg font-bold text-blue-700">{{ \App\Models\StoreSetting::get('max_delivery_distance', 15) }} km</span>
                                    <span class="text-xs sm:text-sm text-blue-600">dari toko</span>
                                </div>
                            </div>
                            <p class="text-xs text-blue-600 mt-2 sm:mt-3">
                                💡 <strong>Tips:</strong> Pastikan alamat Anda berada dalam radius jarak pengiriman untuk dapat menggunakan layanan pengiriman.
                            </p>
                        </div>
                    </div>
                </div>


                <!-- Address Form -->
                @if($showAddressForm)
                <div class="bg-base-200 p-3 sm:p-4 lg:p-6 rounded-lg mb-4 sm:mb-6">
                    <div class="flex justify-between items-center mb-3 sm:mb-4">
                        <h3 class="text-base sm:text-lg font-semibold">
                            {{ $editingAddressId ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
                        </h3>
                        <button wire:click="hideAddressForm" class="btn btn-ghost btn-xs sm:btn-sm">
                            <x-icons.x class="w-3 h-3 sm:w-4 sm:h-4" />
                        </button>
                    </div>

                    <!-- Use Address Form Component -->
                    <form wire:submit="saveAddress">
                        <x-address-form
                            :addressForm="$addressForm"
                            :provinces="$provinces"
                            :regencies="$regencies"
                            :subDistricts="$subDistricts"
                            :villages="$villages"
                            :postalCodes="$postalCodes"
                            :addressPreview="$addressPreview"
                            :editingAddressId="$editingAddressId"
                            cancelAction="hideAddressForm" />
                    </form>
                </div>
                @endif

                <!-- Address List -->
                <div class="space-y-3 sm:space-y-4">
                    @forelse($addresses as $address)
                    <div wire:key="address-{{ $address['address_id'] }}" class="border border-base-300 rounded-lg p-3 sm:p-4 {{ $address['is_default'] ? 'border-primary bg-primary/5' : '' }}">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-1 sm:gap-2 mb-2">
                                    <span class="badge badge-primary badge-xs sm:badge-sm text-xs">{{ ucfirst($address['label']) }}</span>
                                    @if($address['is_default'])
                                    <span class="badge badge-success badge-xs sm:badge-sm text-xs">Alamat Utama</span>
                                    @endif
                                </div>
                                <h4 class="font-semibold text-base sm:text-lg truncate">{{ $address['recipient_name'] }}</h4>
                                <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ $address['phone'] }}</p>
                                <p class="text-xs sm:text-sm mb-2 break-words">{{ $address['detailed_address'] }}</p>
                                <p class="text-xs sm:text-sm text-gray-600 break-words">
                                    {{ $address['district'] }}, {{ $address['city'] }} {{ $address['postal_code'] }}
                                </p>
                                @if($address['notes'])
                                <p class="text-xs sm:text-sm text-gray-500 mt-1 italic break-words">{{ $address['notes'] }}</p>
                                @endif
                            </div>
                            <div class="dropdown dropdown-end flex-shrink-0">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-xs sm:btn-sm">
                                    <div class="flex flex-col items-center justify-center w-3 h-3 sm:w-4 sm:h-4">
                                        <div class="w-0.5 h-0.5 sm:w-1 sm:h-1 bg-current rounded-full mb-0.5"></div>
                                        <div class="w-0.5 h-0.5 sm:w-1 sm:h-1 bg-current rounded-full mb-0.5"></div>
                                        <div class="w-0.5 h-0.5 sm:w-1 sm:h-1 bg-current rounded-full"></div>
                                    </div>
                                </div>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-44 sm:w-52 p-2 shadow-xl border border-base-300">
                                    @if(!$address['is_default'])
                                    <li>
                                        <button wire:click="setDefaultAddress({{ $address['address_id'] }})"
                                            onclick="this.closest('.dropdown').querySelector('[role=button]').blur()"
                                            class="w-full text-left flex items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                                            <x-icons.star class="w-3 h-3 sm:w-4 sm:h-4" />
                                            Jadikan Utama
                                        </button>
                                    </li>
                                    @endif
                                    <li>
                                        <button wire:click="editAddress({{ $address['address_id'] }})"
                                            onclick="this.closest('.dropdown').querySelector('[role=button]').blur()"
                                            class="w-full text-left flex items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                                            <x-icons.edit class="w-3 h-3 sm:w-4 sm:h-4" />
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button wire:click="confirmDeleteAddress({{ $address['address_id'] }})"
                                            onclick="this.closest('.dropdown').querySelector('[role=button]').blur()"
                                            class="w-full text-left text-error flex items-center gap-1 sm:gap-2 text-xs sm:text-sm">
                                            <x-icons.trash class="w-3 h-3 sm:w-4 sm:h-4" />
                                            Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 sm:py-8">
                        <x-icons.map-pin class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 mb-3 sm:mb-4" />
                        <p class="text-sm sm:text-base text-gray-500 mb-3 sm:mb-4">Belum ada alamat tersimpan</p>
                        <button wire:click="openAddressForm" class="btn btn-success btn-sm sm:btn-md">
                            <x-icons.plus class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" />
                            <span class="text-xs sm:text-sm">Tambah Alamat Pertama</span>
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Address Confirmation Modal -->
    @if($showDeleteModal)
    <div class="modal modal-open">
        <div class="modal-box max-w-sm sm:max-w-md">
            <h3 class="font-bold text-base sm:text-lg text-error mb-3 sm:mb-4">
                <span class="text-error text-lg sm:text-xl mr-1 sm:mr-2">⚠️</span>
                <span class="text-sm sm:text-base">Konfirmasi Hapus Alamat</span>
            </h3>

            <div class="py-3 sm:py-4">
                <p class="text-xs sm:text-sm text-gray-700 mb-3 sm:mb-4">Apakah Anda yakin ingin menghapus alamat berikut?</p>

                @if($addressToDeleteData)
                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg border">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <div class="flex-shrink-0">
                            <span class="badge badge-outline badge-xs sm:badge-sm text-xs">{{ ucfirst($addressToDeleteData['label'] ?? '') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $addressToDeleteData['recipient_name'] ?? '' }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1 break-words">{{ $addressToDeleteData['detailed_address'] ?? '' }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 break-words">{{ $addressToDeleteData['village_name'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="alert alert-warning mt-3 sm:mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <span class="text-xs sm:text-sm">Tindakan ini tidak dapat dibatalkan. Alamat akan dihapus secara permanen.</span>
                </div>
            </div>

            <div class="modal-action flex-col-reverse sm:flex-row gap-2 sm:gap-0">
                <button wire:click="cancelDeleteAddress" class="btn btn-ghost btn-sm sm:btn-md">
                    <x-icons.x class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" />
                    <span class="text-xs sm:text-sm">Batal</span>
                </button>
                <button wire:click="deleteAddress" class="btn btn-error btn-sm sm:btn-md">
                    <span wire:loading.remove>
                        <x-icons.trash class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" />
                        <span class="text-xs sm:text-sm">Ya, Hapus Alamat</span>
                    </span>
                    <span wire:loading>
                        <span class="loading loading-spinner loading-xs sm:loading-sm mr-1 sm:mr-2"></span>
                        <span class="text-xs sm:text-sm">Menghapus...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
