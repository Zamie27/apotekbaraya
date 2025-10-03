<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Manajemen Pengguna</h1>
            <p class="text-gray-600">Kelola akun pengguna sistem apotek</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @livewire('admin.create-user')
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pencarian</span>
                    </label>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Cari nama, username, email, atau telepon..." 
                           class="input input-bordered w-full">
                </div>

                <!-- Role Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Filter Role</span>
                    </label>
                    <select wire:model.live="roleFilter" class="select select-bordered w-full">
                        <option value="all">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Filter Status</span>
                    </label>
                    <select wire:model.live="statusFilter" class="select select-bordered w-full">
                        <option value="all">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Tampilkan</span>
                    </label>
                    <select wire:model.live="perPage" class="select select-bordered w-full">
                        <option value="10">10 per halaman</option>
                        <option value="25">25 per halaman</option>
                        <option value="50">50 per halaman</option>
                        <option value="100">100 per halaman</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>
                                <button wire:click="sortBy('name')" class="flex items-center space-x-1 hover:text-primary">
                                    <span>Nama</span>
                                    @if($sortBy === 'name')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Role</th>
                            <th>
                                <button wire:click="sortBy('status')" class="flex items-center space-x-1 hover:text-primary">
                                    <span>Status</span>
                                    @if($sortBy === 'status')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th>
                                <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-primary">
                                    <span>Terdaftar</span>
                                    @if($sortBy === 'created_at')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortDirection === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </button>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12">
                                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $user->name }}</div>
                                            @if($user->gender)
                                                <div class="text-sm opacity-50">{{ ucfirst($user->gender) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono text-sm">{{ $user->username }}</span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    <div class="badge badge-outline badge-{{ $user->role->name === 'admin' ? 'error' : ($user->role->name === 'apoteker' ? 'warning' : ($user->role->name === 'kurir' ? 'info' : 'success')) }}">
                                        {{ ucfirst($user->role->name) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="badge badge-{{ $user->status === 'active' ? 'success' : 'error' }}">
                                        {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        {{ $user->created_at->format('d/m/Y') }}
                                        <div class="text-xs opacity-50">{{ $user->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button wire:click="editUser({{ $user->user_id }})" 
                                                class="btn btn-sm btn-ghost btn-square tooltip" 
                                                data-tip="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        @if($user->user_id !== auth()->id())
                                            <button wire:click="openDeleteModal({{ $user->user_id }})" 
                                                    class="btn btn-sm btn-ghost btn-square text-error tooltip" 
                                                    data-tip="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8">
                                    <div class="flex flex-col items-center space-y-2">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <span class="text-gray-500">Tidak ada pengguna ditemukan</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="flex justify-center py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create User Modal -->
    @if($showCreateModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-2xl">
                <h3 class="font-bold text-lg mb-4">Tambah Pengguna Baru</h3>
                
                <form wire:submit.prevent="createUser">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nama Lengkap <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="name" class="input input-bordered @error('name') input-error @enderror" placeholder="Masukkan nama lengkap">
                            @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Username <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="username" class="input input-bordered @error('username') input-error @enderror" placeholder="Masukkan username">
                            @error('username') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email <span class="text-error">*</span></span>
                            </label>
                            <input type="email" wire:model="email" class="input input-bordered @error('email') input-error @enderror" placeholder="Masukkan email">
                            @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nomor Telepon <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="phone" class="input input-bordered @error('phone') input-error @enderror" placeholder="Masukkan nomor telepon">
                            @error('phone') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Password <span class="text-error">*</span></span>
                            </label>
                            <input type="password" wire:model="password" class="input input-bordered @error('password') input-error @enderror" placeholder="Masukkan password">
                            @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Konfirmasi Password <span class="text-error">*</span></span>
                            </label>
                            <input type="password" wire:model="password_confirmation" class="input input-bordered" placeholder="Konfirmasi password">
                        </div>

                        <!-- Role -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Role <span class="text-error">*</span></span>
                            </label>
                            <select wire:model="role_id" class="select select-bordered @error('role_id') select-error @enderror">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Status</span>
                            </label>
                            <select wire:model="status" class="select select-bordered">
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- Date of Birth -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Tanggal Lahir</span>
                            </label>
                            <input type="date" wire:model="date_of_birth" class="input input-bordered @error('date_of_birth') input-error @enderror">
                            @error('date_of_birth') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Gender -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Jenis Kelamin</span>
                            </label>
                            <select wire:model="gender" class="select select-bordered">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModals" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit User Modal -->
    @if($showEditModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-2xl">
                <h3 class="font-bold text-lg mb-4">Edit Pengguna</h3>
                
                <form wire:submit.prevent="updateUser">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nama Lengkap <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="name" class="input input-bordered @error('name') input-error @enderror" placeholder="Masukkan nama lengkap">
                            @error('name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Username <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="username" class="input input-bordered @error('username') input-error @enderror" placeholder="Masukkan username">
                            @error('username') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Email <span class="text-error">*</span></span>
                            </label>
                            <input type="email" wire:model="email" class="input input-bordered @error('email') input-error @enderror" placeholder="Masukkan email">
                            @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nomor Telepon <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="phone" class="input input-bordered @error('phone') input-error @enderror" placeholder="Masukkan nomor telepon">
                            @error('phone') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Password Baru</span>
                                <span class="label-text-alt">Kosongkan jika tidak ingin mengubah</span>
                            </label>
                            <input type="password" wire:model="password" class="input input-bordered @error('password') input-error @enderror" placeholder="Masukkan password baru">
                            @error('password') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Konfirmasi Password</span>
                            </label>
                            <input type="password" wire:model="password_confirmation" class="input input-bordered" placeholder="Konfirmasi password baru">
                        </div>

                        <!-- Role -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Role <span class="text-error">*</span></span>
                            </label>
                            <select wire:model="role_id" class="select select-bordered @error('role_id') select-error @enderror">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Status</span>
                            </label>
                            <select wire:model="status" class="select select-bordered">
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- Date of Birth -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Tanggal Lahir</span>
                            </label>
                            <input type="date" wire:model="date_of_birth" class="input input-bordered @error('date_of_birth') input-error @enderror">
                            @error('date_of_birth') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Gender -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Jenis Kelamin</span>
                            </label>
                            <select wire:model="gender" class="select select-bordered">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModals" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                <p class="py-4">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="modal-action">
                    <button wire:click="closeModals" class="btn btn-ghost">Batal</button>
                    <button wire:click="deleteUser" class="btn btn-error">Hapus</button>
                </div>
            </div>
        </div>
    @endif

</div>