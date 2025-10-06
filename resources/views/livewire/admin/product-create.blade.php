<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Tambah Produk</h1>
        <a href="{{ route('admin.products') }}" class="btn">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Nama Produk</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="form.name" />
                    @error('form.name')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Slug</label>
                    <input type="text" class="input input-bordered w-full" wire:model.defer="form.slug" />
                    @error('form.slug')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Harga</label>
                    <input type="number" step="0.01" class="input input-bordered w-full" wire:model.defer="form.price" />
                    @error('form.price')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Stok</label>
                    <input type="number" class="input input-bordered w-full" wire:model.defer="form.stock" />
                    @error('form.stock')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Kategori</label>
                    <select class="select select-bordered w-full" wire:model.defer="form.category_id">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('form.category_id')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Diskon (%)</label>
                    <input type="number" step="0.01" class="input input-bordered w-full" wire:model.defer="form.discount_percentage" />
                    @error('form.discount_percentage')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label">Unit</label>
                    <select class="select select-bordered w-full" wire:model.defer="form.unit">
                        <option value="">Pilih unit</option>
                        <option value="pcs">Pcs</option>
                        <option value="box">Box</option>
                        <option value="botol">Botol</option>
                        <option value="strip">Strip</option>
                        <option value="tube">Tube</option>
                        <option value="sachet">Sachet</option>
                    </select>
                    @error('form.unit')<span class="text-error text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="flex items-center space-x-6">
                    <label class="label cursor-pointer">
                        <span class="label-text">Perlu Resep</span>
                        <input type="checkbox" class="toggle" wire:model.defer="form.requires_prescription" />
                    </label>
                    <label class="label cursor-pointer">
                        <span class="label-text">Aktif</span>
                        <input type="checkbox" class="toggle" wire:model.defer="form.is_active" />
                    </label>
                </div>
            </div>

            <div>
                <label class="label">Deskripsi</label>
                <textarea class="textarea textarea-bordered w-full" rows="5" wire:model.defer="form.description"></textarea>
                @error('form.description')<span class="text-error text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="label">Spesifikasi Produk</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Kemasan</label>
                        <select class="select select-bordered w-full" wire:model.defer="form.specifications.kemasan">
                            <option value="">Pilih kemasan</option>
                            <option value="Strip">Strip</option>
                            <option value="Botol">Botol</option>
                            <option value="Box">Box</option>
                            <option value="Sachet">Sachet</option>
                            <option value="Tube">Tube</option>
                        </select>
                    </div>
                    <div>
                        <label class="label-text">Produsen</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.produsen" placeholder="cth: PT Kimia Farma" />
                    </div>
                    <div>
                        <label class="label-text">Komposisi</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.komposisi" placeholder="cth: Bahan-bahan penyusun" />
                    </div>
                    <div>
                        <label class="label-text">Manfaat</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.manfaat" placeholder="cth: Meredakan demam dan nyeri" />
                    </div>
                    <div>
                        <label class="label-text">Dosis</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.dosis" placeholder="cth: 3x sehari" />
                    </div>
                    <div>
                        <label class="label-text">Efek Samping</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.efek_samping" placeholder="cth: Mual, pusing" />
                    </div>
                </div>
                <div class="mt-3 text-sm opacity-70">Opsional: Anda bisa mengisi salah satu atau gunakan input JSON di bawah untuk spesifikasi lengkap.</div>
                @error('form.specifications')<span class="text-error text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="label">Spesifikasi (JSON Opsional)</label>
                <textarea class="textarea textarea-bordered w-full" rows="4" wire:model.defer="specifications_json" placeholder='{"Kemasan":"Strip","Produsen":"PT Kimia Farma","Komposisi":"","Manfaat":"","Dosis":"","Efek Samping":""}'></textarea>
                <div class="text-sm opacity-70 mt-1">Gunakan JSON valid. Import CSV menggunakan header: unit, kemasan, produsen, komposisi, manfaat, dosis, efek_samping. Catatan: "kandungan" tidak digunakan, pakai "komposisi".</div>
                @error('specifications_json')<span class="text-error text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="label">Gambar Produk</label>
                <input type="file" class="file-input file-input-bordered w-full" wire:model="image" accept=".jpg,.jpeg,.png,.webp" />
                @error('image')<span class="text-error text-sm">{{ $message }}</span>@enderror
                @if($image)
                    <div class="mt-3 inline-block border border-base-300 rounded-lg p-2 bg-base-200">
                        <div class="w-64 h-64 rounded-lg overflow-hidden">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="object-cover w-full h-full" />
                        </div>
                    </div>
                    <div class="text-xs opacity-70 mt-2">Preview diperbesar untuk memudahkan pengecekan detail gambar.</div>
                @endif
            </div>

            <div class="text-right">
                <button class="btn btn-primary" wire:click="save">Simpan</button>
            </div>
        </div>
    </div>
</div>