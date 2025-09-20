<div>
    <!-- Edit User Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit.prevent="updateUser">
                    <!-- Modal Header -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                                Edit Pengguna
                            </h3>
                            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Nama Lengkap <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" wire:model="name" class="input input-bordered @error('name') input-error @enderror" placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Username <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" wire:model.blur="username" class="input input-bordered @error('username') input-error @enderror" placeholder="Masukkan username">
                                @error('username')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Email <span class="text-red-500">*</span></span>
                                </label>
                                <input type="email" wire:model.blur="email" class="input input-bordered @error('email') input-error @enderror" placeholder="Masukkan email">
                                @error('email')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Nomor Telepon <span class="text-red-500">*</span></span>
                                </label>
                                <input type="text" wire:model.blur="phone" class="input input-bordered @error('phone') input-error @enderror" placeholder="Masukkan nomor telepon">
                                @error('phone')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Password Baru</span>
                                    <span class="label-text-alt text-gray-500">(Kosongkan jika tidak ingin mengubah)</span>
                                </label>
                                <input type="password" wire:model="password" class="input input-bordered @error('password') input-error @enderror" placeholder="Masukkan password baru">
                                @error('password')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Konfirmasi Password</span>
                                </label>
                                <input type="password" wire:model="password_confirmation" class="input input-bordered @error('password_confirmation') input-error @enderror" placeholder="Konfirmasi password baru">
                                @error('password_confirmation')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Role <span class="text-red-500">*</span></span>
                                </label>
                                <select wire:model="role_id" class="select select-bordered @error('role_id') select-error @enderror">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Status <span class="text-red-500">*</span></span>
                                </label>
                                <select wire:model="status" class="select select-bordered @error('status') select-error @enderror">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Jenis Kelamin</span>
                                </label>
                                <select wire:model="gender" class="select select-bordered @error('gender') select-error @enderror">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                                @error('gender')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium">Tanggal Lahir</span>
                                </label>
                                <input type="date" wire:model="date_of_birth" class="input input-bordered @error('date_of_birth') input-error @enderror">
                                @error('date_of_birth')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span class="label-text font-medium">Alamat</span>
                                </label>
                                <textarea wire:model="address" class="textarea textarea-bordered @error('address') textarea-error @enderror" placeholder="Masukkan alamat lengkap" rows="3"></textarea>
                                @error('address')
                                    <label class="label">
                                        <span class="label-text-alt text-red-500">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <span wire:loading.remove>Perbarui</span>
                            <span wire:loading>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memperbarui...
                            </span>
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success mt-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mt-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
</div>
