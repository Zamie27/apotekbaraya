<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Manajemen Kategori</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products') }}" class="btn">Kelola Produk</a>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <input type="text" wire:model.debounce.500ms="search" placeholder="Cari kategori..." class="input input-bordered w-full" />
        <select wire:model="active" class="select select-bordered w-full">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
        <div class="text-right">
            <span class="opacity-70">Klik kategori untuk melihat detail</span>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto bg-base-100 rounded-lg shadow">
        <table class="table w-full">
            <thead>
                <tr>
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
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-ghost">Nonaktif</span>
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
                        <td colspan="4" class="text-center py-6">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                    <button class="btn btn-error" wire:click="deleteCategory(confirmDeleteId)">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>