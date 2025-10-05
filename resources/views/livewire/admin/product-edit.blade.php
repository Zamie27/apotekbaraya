<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Edit Produk</h1>
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
                            <option value="{{ $cat->category_id }}" @selected($cat->category_id == $form['category_id'])>{{ $cat->name }}</option>
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
                    <label class="label">Berat (gram)</label>
                    <input type="number" step="0.01" class="input input-bordered w-full" wire:model.defer="form.weight" />
                    @error('form.weight')<span class="text-error text-sm">{{ $message }}</span>@enderror
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
                        <label class="label-text">Kandungan</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.kandungan" placeholder="cth: Paracetamol 500mg" />
                    </div>
                    <div>
                        <label class="label-text">Kemasan</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.kemasan" placeholder="cth: Strip, Botol, Box" />
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
                    <div>
                        <label class="label-text">Lainnya</label>
                        <input type="text" class="input input-bordered w-full" wire:model.defer="form.specifications.lainnya" placeholder="cth: Informasi tambahan" />
                    </div>
                </div>
                <div class="mt-3 text-sm opacity-70">Opsional: gunakan input JSON di bawah untuk spesifikasi lengkap.</div>
                @error('form.specifications')<span class="text-error text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="label">Spesifikasi (JSON Opsional)</label>
                <textarea class="textarea textarea-bordered w-full" rows="4" wire:model.defer="specifications_json" placeholder='{"Kandungan":"Paracetamol 500mg","Kemasan":"Strip","Produsen":"PT Kimia Farma","Komposisi":"","Manfaat":"","Dosis":"","Efek Samping":"","Lainnya":""}'></textarea>
                <div class="text-sm opacity-70 mt-1">Gunakan JSON valid. Import CSV: kandungan, kemasan, produsen, komposisi, manfaat, dosis, efek_samping, lainnya.</div>
                @error('specifications_json')<span class="text-error text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="label">Gambar Produk</label>
                <input type="file" class="file-input file-input-bordered w-full" wire:model="image" accept=".jpg,.jpeg,.png,.webp" />
                @error('image')<span class="text-error text-sm">{{ $message }}</span>@enderror
                <div class="mt-2 flex items-center space-x-3">
                    @if($image)
                        <div class="avatar">
                            <div class="w-16 rounded">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview" />
                            </div>
                        </div>
                    @elseif(!empty($product->primary_image_url))
                        <div class="avatar">
                            <div class="w-16 rounded">
                                <img src="{{ $product->primary_image_url }}" alt="Gambar produk" />
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <button class="btn btn-primary" wire:click="save">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>