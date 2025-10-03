<div>
    <!-- Edit User Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-4xl">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-base-content flex items-center">
                        <i class="fas fa-user-edit text-primary mr-3"></i>
                        Edit Pengguna
                    </h3>
                    <button type="button" wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="updateUser">
                    <div class="bg-base-100 rounded-lg border border-base-300 p-6">

                    <!-- Form Fields -->
                    <div class="space-y-6">
                        <!-- Personal Information Section -->
                        <div>
                            <h4 class="text-lg font-semibold text-base-content mb-4 flex items-center">
                                <i class="fas fa-user text-primary mr-2"></i>
                                Informasi Personal
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Nama Lengkap <span class="text-error">*</span></span>
                                    </label>
                                    <input type="text" wire:model="name" class="input input-bordered focus:input-primary @error('name') input-error @enderror" placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Username -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Username <span class="text-error">*</span></span>
                                    </label>
                                    <input type="text" wire:model.blur="username" class="input input-bordered focus:input-primary @error('username') input-error @enderror" placeholder="Masukkan username">
                                    @error('username')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Email <span class="text-error">*</span></span>
                                    </label>
                                    <input type="email" wire:model.blur="email" class="input input-bordered focus:input-primary @error('email') input-error @enderror" placeholder="Masukkan email">
                                    @error('email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Nomor Telepon <span class="text-error">*</span></span>
                                    </label>
                                    <input type="text" wire:model.blur="phone" class="input input-bordered focus:input-primary @error('phone') input-error @enderror" placeholder="Masukkan nomor telepon">
                                    @error('phone')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Jenis Kelamin</span>
                                    </label>
                                    <select wire:model="gender" class="select select-bordered focus:select-primary @error('gender') select-error @enderror">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Tanggal Lahir</span>
                                    </label>
                                    <input type="date" wire:model="date_of_birth" class="input input-bordered focus:input-primary @error('date_of_birth') input-error @enderror">
                                    @error('date_of_birth')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>


                        </div>

                        <!-- Security Section -->
                        <div>
                            <h4 class="text-lg font-semibold text-base-content mb-4 flex items-center">
                                <i class="fas fa-lock text-warning mr-2"></i>
                                Keamanan & Akses
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <!-- Password -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Password Baru</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" wire:model="password" class="input input-bordered focus:input-primary @error('password') input-error @enderror" placeholder="Kosongkan jika tidak ingin mengubah">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i class="fas fa-key text-base-content/50"></i>
                                        </div>
                                    </div>
                                    @error('password')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Password Confirmation -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Konfirmasi Password</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" wire:model="password_confirmation" class="input input-bordered focus:input-primary @error('password_confirmation') input-error @enderror" placeholder="Konfirmasi password baru">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i class="fas fa-lock text-base-content/50"></i>
                                        </div>
                                    </div>
                                    @error('password_confirmation')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Role -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Role <span class="text-error">*</span></span>
                                    </label>
                                    <select wire:model="role" class="select select-bordered focus:select-primary @error('role') select-error @enderror">
                                        <option value="">Pilih Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="apoteker">Apoteker</option>
                                        <option value="kurir">Kurir</option>
                                        <option value="pelanggan">Pelanggan</option>
                                    </select>
                                    @error('role')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium">Status <span class="text-error">*</span></span>
                                    </label>
                                    <select wire:model="status" class="select select-bordered focus:select-primary @error('status') select-error @enderror">
                                        <option value="">Pilih Status</option>
                                        <option value="active">
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            Aktif
                                        </option>
                                        <option value="inactive">
                                            <i class="fas fa-times-circle text-error mr-2"></i>
                                            Tidak Aktif
                                        </option>
                                    </select>
                                    @error('status')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-base-300 bg-base-50 -mx-6 -mb-6 px-6 py-4 rounded-b-lg">
                        <button type="button" wire:click="closeModal" class="btn btn-outline btn-md">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-md" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-save mr-2"></i>
                                Perbarui
                            </span>
                            <span wire:loading class="flex items-center">
                                <span class="loading loading-spinner loading-sm mr-2"></span>
                                Memperbarui...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Success Message -->
                @if (session()->has('message'))
                    <div class="alert alert-success mt-4">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif

                <!-- Error Message -->
                @if (session()->has('error'))
                    <div class="alert alert-error mt-4">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
