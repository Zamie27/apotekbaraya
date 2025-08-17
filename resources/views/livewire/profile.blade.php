<div class="max-w-4xl mx-auto p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profil Saya</h1>
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
                                placeholder="Masukkan nama lengkap">
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
                                placeholder="Masukkan username">
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
                                placeholder="Masukkan email">
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
                                placeholder="Masukkan nomor telepon">
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
                                class="input input-bordered w-full @error('date_of_birth') input-error @enderror">
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
                            <div class="w-24 h-24 rounded-full ring ring-success ring-offset-base-100 ring-offset-2">
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
                                class="file-input file-input-bordered file-input-sm w-full @error('avatar') file-input-error @enderror">
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
                            wire:confirm="Apakah Anda yakin ingin menghapus foto profil?">
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

    <!-- Address Management Section -->
    <div class="mt-8">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="card-title text-xl">
                        <x-icons.map-pin class="w-5 h-5" />
                        Alamat Saya
                    </h2>
                    <button wire:click="openAddressForm" class="btn btn-primary btn-sm">
                        <x-icons.plus class="w-4 h-4 mr-2" />
                        Tambah Alamat
                    </button>
                </div>

                <!-- Address Form -->
                @if($showAddressForm)
                <div class="bg-base-200 p-6 rounded-lg mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                            {{ $editingAddressId ? 'Edit Alamat' : 'Tambah Alamat Baru' }}
                        </h3>
                        <button wire:click="hideAddressForm" class="btn btn-ghost btn-sm">
                            <x-icons.x class="w-4 h-4" />
                        </button>
                    </div>

                    <form wire:submit="saveAddress" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Label -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Label Alamat</span>
                                </label>
                                <select wire:model="address_label" class="select select-bordered w-full @error('address_label') select-error @enderror">
                                    <option value="rumah">Rumah</option>
                                    <option value="kantor">Kantor</option>
                                    <option value="kost">Kost</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                                @error('address_label')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Recipient Name -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Nama Penerima <span class="text-red-500">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="recipient_name"
                                    class="input input-bordered w-full @error('recipient_name') input-error @enderror"
                                    placeholder="Nama penerima"
                                    required>
                                @error('recipient_name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nomor Telepon <span class="text-red-500">*</span></span>
                            </label>
                            <input
                                type="text"
                                wire:model="address_phone"
                                class="input input-bordered w-full @error('address_phone') input-error @enderror"
                                placeholder="Nomor telepon penerima"
                                required>
                            @error('address_phone')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>



                        <!-- Detailed Location Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Village -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Desa/Kelurahan <span class="text-red-500">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="village"
                                    class="input input-bordered w-full @error('village') input-error @enderror"
                                    placeholder="Nama desa/kelurahan"
                                    required>
                                @error('village')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Sub District -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Kecamatan <span class="text-red-500">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="sub_district"
                                    class="input input-bordered w-full @error('sub_district') input-error @enderror"
                                    placeholder="Nama kecamatan"
                                    required>
                                @error('sub_district')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Regency -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Kabupaten/Kota <span class="text-red-500">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="regency"
                                    class="input input-bordered w-full @error('regency') input-error @enderror"
                                    placeholder="Kabupaten/Kota">
                                @error('regency')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Province -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Provinsi <span class="text-red-500">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="province"
                                    class="input input-bordered w-full @error('province') input-error @enderror"
                                    placeholder="Nama provinsi">
                                @error('province')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            <!-- Postal Code -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Kode Pos <span class="text-red-500">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="postal_code"
                                    class="input input-bordered w-full @error('postal_code') input-error @enderror"
                                    placeholder="Kode pos">
                                @error('postal_code')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>
                        </div>

                        <!-- Detailed Address for Courier -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Alamat Lengkap untuk Kurir <span class="text-red-500">*</span></span>
                            </label>
                            <textarea
                                wire:model="detailed_address"
                                class="textarea textarea-bordered w-full @error('detailed_address') textarea-error @enderror"
                                placeholder="Deskripsi detail lokasi, patokan, warna rumah, dll. untuk memudahkan kurir menemukan alamat"
                                rows="3"
                                required></textarea>
                            @error('detailed_address')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Hidden fields for backward compatibility -->
                        <input type="hidden" wire:model="district">
                        <input type="hidden" wire:model="city">

                        <!-- Notes -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Catatan (Opsional)</span>
                            </label>
                            <textarea
                                wire:model="notes"
                                class="textarea textarea-bordered w-full @error('notes') textarea-error @enderror"
                                placeholder="Patokan atau catatan tambahan"
                                rows="2"></textarea>
                            @error('notes')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Default Address Checkbox -->
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start">
                                <input type="checkbox" wire:model="is_default" class="checkbox checkbox-primary mr-3" />
                                <span class="label-text">Jadikan alamat utama</span>
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-2 pt-4">
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove>
                                    <x-icons.save class="w-4 h-4 mr-2" />
                                    {{ $editingAddressId ? 'Update Alamat' : 'Simpan Alamat' }}
                                </span>
                                <span wire:loading>
                                    <span class="loading loading-spinner loading-sm mr-2"></span>
                                    Menyimpan...
                                </span>
                            </button>
                            <button type="button" wire:click="hideAddressForm" class="btn btn-ghost">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Address List -->
                <div class="space-y-4">
                    @forelse($addresses as $address)
                    <div wire:key="address-{{ $address['address_id'] }}" class="border border-base-300 rounded-lg p-4 {{ $address['is_default'] ? 'border-primary bg-primary/5' : '' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="badge badge-primary badge-sm">{{ ucfirst($address['label']) }}</span>
                                    @if($address['is_default'])
                                    <span class="badge badge-success badge-sm">Alamat Utama</span>
                                    @endif
                                </div>
                                <h4 class="font-semibold text-lg">{{ $address['recipient_name'] }}</h4>
                                <p class="text-sm text-gray-600 mb-1">{{ $address['phone'] }}</p>
                                <p class="text-sm mb-2">{{ $address['detailed_address'] }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $address['district'] }}, {{ $address['city'] }} {{ $address['postal_code'] }}
                                </p>
                                @if($address['notes'])
                                <p class="text-sm text-gray-500 mt-1 italic">{{ $address['notes'] }}</p>
                                @endif
                            </div>
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                    <div class="flex flex-col items-center justify-center w-4 h-4">
                                        <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                                        <div class="w-1 h-1 bg-current rounded-full mb-0.5"></div>
                                        <div class="w-1 h-1 bg-current rounded-full"></div>
                                    </div>
                                </div>
                                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-xl border border-base-300">
                                    @if(!$address['is_default'])
                                    <li>
                                        <button wire:click="setDefaultAddress({{ $address['address_id'] }})"
                                            onclick="this.closest('.dropdown').querySelector('[role=button]').blur()"
                                            class="w-full text-left flex items-center gap-2">
                                            <x-icons.star class="w-4 h-4" />
                                            Jadikan Utama
                                        </button>
                                    </li>
                                    @endif
                                    <li>
                                        <button wire:click="editAddress({{ $address['address_id'] }})"
                                            onclick="this.closest('.dropdown').querySelector('[role=button]').blur()"
                                            class="w-full text-left flex items-center gap-2">
                                            <x-icons.edit class="w-4 h-4" />
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button wire:click="deleteAddress({{ $address['address_id'] }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus alamat ini?"
                                            onclick="this.closest('.dropdown').querySelector('[role=button]').blur()"
                                            class="w-full text-left text-error flex items-center gap-2">
                                            <x-icons.trash class="w-4 h-4" />
                                            Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <x-icons.map-pin class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <p class="text-gray-500 mb-4">Belum ada alamat tersimpan</p>
                        <button wire:click="openAddressForm" class="btn btn-primary btn-sm">
                            <x-icons.plus class="w-4 h-4 mr-2" />
                            Tambah Alamat Pertama
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


</div>