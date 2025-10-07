<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Manajemen Produk</h1>
            <p class="text-gray-600">Kelola produk, impor CSV, dan ekspor data</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah Produk</a>
            <button type="button" class="btn btn-sm" wire:click="openImportModal">Impor CSV</button>
            <a href="{{ route('admin.products.export.csv') }}" class="btn btn-sm">Ekspor CSV</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <!-- Filters and Search -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label class="label">
                        <span class="label-text font-medium">Cari Produk</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari berdasarkan nama atau slug produk..." 
                        class="input input-bordered w-full"
                    >
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="label">
                        <span class="label-text font-medium">Filter Kategori</span>
                    </label>
                    <select wire:model.live="categoryId" class="select select-bordered w-full">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="label">
                        <span class="label-text font-medium">Filter Status</span>
                    </label>
                    <select wire:model.live="active" class="select select-bordered w-full">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="label">
                        <span class="label-text font-medium">Tampilkan per halaman</span>
                    </label>
                    <select wire:model.live="perPage" class="select select-bordered w-full">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="{{ route('admin.categories') }}" class="btn">Kelola Kategori</a>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr wire:key="product-{{ $product->product_id }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-16 h-16">
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-full" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ $product->name }}</div>
                                            <div class="text-sm opacity-70">SKU: {{ $product->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.detail', $product->category_id) }}" class="link">{{ optional($product->category)->name ?? '-' }}</a>
                                </td>
                                <td>
                                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    @if($product->is_on_sale)
                                        <div class="text-xs text-success">Diskon {{ $product->discount_percentage }}%</div>
                                    @endif
                                </td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge badge-success badge-sm">Aktif</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-sm">Edit</a>
                                    <button type="button" class="btn btn-error btn-sm" wire:click="$set('confirmDeleteId', {{ $product->product_id }})">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="text-gray-500">Belum ada produk yang ditambahkan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    @error('importFile')
        <div class="alert alert-error mt-2">{{ $message }}</div>
    @enderror

    <!-- Import Modal -->
    @if($showImportModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-xl">
                <form wire:submit.prevent="importCsv">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Impor Produk via CSV</h3>
                        <button type="button" class="btn btn-sm btn-circle btn-ghost" wire:click="closeImportModal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="label">Pilih File CSV</label>
                            <input type="file" wire:model="importFile" accept=".csv" class="file-input file-input-bordered w-full" />
                            @error('importFile')<span class="text-error text-sm">{{ $message }}</span>@enderror
                            <div class="text-sm opacity-70 mt-2">
                                Header minimal yang dibutuhkan: <code>name, category_slug, price, unit</code>.
                            </div>
                            <div class="text-sm opacity-70">
                                Header tambahan (opsional): <code>slug, discount_percentage, stock, requires_prescription, is_active, kemasan, produsen, deskripsi, komposisi, manfaat, dosis, efek_samping, lainnya</code>.
                                <span class="block mt-1">Catatan: kolom <code>weight</code> dan <code>kandungan</code> tidak digunakan lagi. Jika <code>komposisi</code> kosong dan <code>kandungan</code> tersedia, sistem akan menggunakan nilai <code>kandungan</code> sebagai <code>komposisi</code>.</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-action">
                        <button type="button" class="btn btn-ghost" wire:click="closeImportModal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Impor</span>
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import Issues Modal -->
    @if($showImportIssuesModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Detail Isu Impor</h3>
                    <button type="button" class="btn btn-sm btn-circle btn-ghost" wire:click="closeImportIssuesModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    @if(!empty($importSummary['updates']))
                        <div>
                            <div class="font-semibold mb-1">Perubahan yang diterapkan ({{ count($importSummary['updates'] ?? []) }}):</div>
                            <ul class="list-disc ml-5 max-h-48 overflow-auto pr-2">
                                @foreach(($importSummary['updates'] ?? []) as $upd)
                                    <li class="text-success">{{ $upd }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($importSummary['errors']))
                        <div>
                            <div class="font-semibold mb-1">Isu ({{ count($importSummary['errors'] ?? []) }}):</div>
                            <ul class="list-disc ml-5 max-h-48 overflow-auto pr-2">
                                @foreach(($importSummary['errors'] ?? []) as $err)
                                    <li class="text-error">{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(empty($importSummary['updates']) && empty($importSummary['errors']))
                        <div class="text-sm">Tidak ada isu atau perubahan untuk ditampilkan.</div>
                    @endif
                </div>
                <div class="modal-action">
                    <button type="button" class="btn" wire:click="closeImportIssuesModal">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if(!is_null($confirmDeleteId))
        <div class="modal modal-open">
            <div class="modal-box">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-bold">Konfirmasi Hapus Produk</h3>
                    <button type="button" class="btn btn-sm btn-circle btn-ghost" wire:click="$set('confirmDeleteId', null)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="mb-4">Anda yakin ingin menghapus produk ini? Tindakan ini akan menghapus file gambar terkait dan tidak dapat dibatalkan.</p>
                <div class="modal-action">
                    <button type="button" class="btn" wire:click="$set('confirmDeleteId', null)">Batal</button>
                    <button type="button" class="btn btn-error" wire:click="deleteProduct({{ $confirmDeleteId }})">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>