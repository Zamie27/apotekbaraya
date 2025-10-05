<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Detail Kategori: {{ $category->name }}</h1>
        <a href="{{ route('admin.categories') }}" class="btn">Kembali</a>
    </div>

    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-sm opacity-70">Slug</div>
                    <div class="font-mono">{{ $category->slug }}</div>
                </div>
                <div>
                    <div class="text-sm opacity-70">Status</div>
                    @if($category->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-ghost">Nonaktif</span>
                    @endif
                </div>
                <div>
                    <div class="text-sm opacity-70">Urutan</div>
                    <div>{{ $category->sort_order }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-base-100 rounded-lg shadow">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->products as $product)
                    <tr>
                        <td>
                            <div class="font-semibold">{{ $product->name }}</div>
                            <div class="text-sm opacity-70">SKU: {{ $product->slug }}</div>
                        </td>
                        <td>Rp {{ number_format($product->final_price, 0, ',', '.') }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            @if($product->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-ghost">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-sm">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6">Belum ada produk untuk kategori ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>