<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Manajemen Kategori</h1>
            <p class="text-gray-600">Kelola kategori produk untuk katalog toko</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products') }}" class="btn">Kelola Produk</a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="label">
                        <span class="label-text font-medium">Cari Kategori</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari berdasarkan nama atau slug kategori..."
                        class="input input-bordered w-full"
                    >
                </div>

                <!-- Status Filter -->
                <div class="lg:w-48">
                    <label class="label">
                        <span class="label-text font-medium">Filter Status</span>
                    </label>
                    <select wire:model.live="active" class="select select-bordered w-full">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <!-- Categories Table -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Nama</th>
                            <th>Produk Aktif</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.categories.detail', $category->category_id) }}" class="link font-semibold">{{ $category->name }}</a>
                                    <div class="text-sm opacity-70">Slug: {{ $category->slug }}</div>
                                </td>
                                <td>{{ $category->products()->count() }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success badge-sm">Aktif</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category->category_id) }}" class="btn btn-sm">Edit</a>
                                        <button class="btn btn-sm btn-error" wire:click="$set('confirmDeleteId', {{ $category->category_id }})">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="text-gray-500">Belum ada kategori yang ditambahkan.</div>
                                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    @if(!is_null($confirmDeleteId))
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-2">Konfirmasi Hapus</h3>
                <p>Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="modal-action">
                    <button class="btn" wire:click="$set('confirmDeleteId', null)">Batal</button>
                    <button class="btn btn-error" wire:click="deleteCategory({{ $confirmDeleteId }})">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>