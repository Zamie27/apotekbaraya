<x-layouts.user title="Upload Resep Dokter">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Upload Resep Dokter</h1>
            <p class="text-gray-600">Unggah foto resep dokter Anda untuk mendapatkan obat yang dibutuhkan</p>
        </div>

        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('customer.prescriptions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Doctor Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Nama Dokter <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" 
                           name="doctor_name" 
                           value="{{ old('doctor_name') }}"
                           placeholder="Masukkan nama dokter yang memberikan resep"
                           class="input input-bordered w-full @error('doctor_name') input-error @enderror" 
                           required>
                    @error('doctor_name')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Patient Name -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Nama Pasien <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text" 
                           name="patient_name" 
                           value="{{ old('patient_name') }}"
                           placeholder="Masukkan nama pasien sesuai resep"
                           class="input input-bordered w-full @error('patient_name') input-error @enderror" 
                           required>
                    @error('patient_name')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Prescription Image Upload -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Foto Resep <span class="text-red-500">*</span></span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                        <input type="file" 
                               name="prescription_image" 
                               id="prescription_image"
                               accept="image/jpeg,image/png,image/jpg"
                               class="hidden"
                               required>
                        <label for="prescription_image" class="cursor-pointer">
                            <div id="upload-area">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-gray-600 mb-2">Klik untuk upload foto resep</p>
                                <p class="text-sm text-gray-500">PNG, JPG, JPEG (Max. 2MB)</p>
                            </div>
                            <div id="preview-area" class="hidden">
                                <img id="preview-image" class="mx-auto max-h-48 rounded-lg mb-2" alt="Preview">
                                <p class="text-sm text-green-600">Foto berhasil dipilih</p>
                            </div>
                        </label>
                    </div>
                    @error('prescription_image')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Catatan Tambahan</span>
                    </label>
                    <textarea name="notes" 
                              placeholder="Tambahkan catatan atau informasi tambahan (opsional)"
                              class="textarea textarea-bordered h-24 @error('notes') textarea-error @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <label class="label">
                            <span class="label-text-alt text-red-500">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <!-- Important Notes -->
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Informasi Penting:</h3>
                        <div class="text-sm mt-1">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Pastikan foto resep jelas dan dapat dibaca</li>
                                <li>Resep harus masih berlaku (tidak kadaluarsa)</li>
                                <li>Apoteker akan memverifikasi resep sebelum memproses pesanan</li>
                                <li>Anda akan mendapat notifikasi status konfirmasi resep</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-4 pt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload Resep
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('prescription_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
            document.getElementById('upload-area').classList.add('hidden');
            document.getElementById('preview-area').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
</x-layouts.user>