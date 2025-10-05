<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Edit Kategori</h1>
        <a href="{{ route('admin.categories') }}" class="btn">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Nama Kategori</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="form.name" />
                    @error('form.name')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Slug</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="form.slug" />
                    @error('form.slug')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Urutan</label>
                    <input type="number" class="input input-bordered w-full" wire:model.defer="form.sort_order" />
                    @error('form.sort_order')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="flex items-center gap-2 mt-8">
                    <input type="checkbox" class="toggle" wire:model.defer="form.is_active" />
                    <span>Aktif</span>
                </div>
            </div>

            <div class="text-right">
                <button wire:click="save" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>