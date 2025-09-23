<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Syarat & Ketentuan</h1>
                <p class="text-lg text-gray-600">Apotek Baraya</p>
                <p class="text-sm text-gray-500 mt-2">Terakhir diperbarui: {{ date('d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <!-- Introduction -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Ketentuan Umum</h2>
                <div class="prose prose-gray max-w-none">
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Selamat datang di Apotek Baraya. Syarat dan Ketentuan ini mengatur penggunaan layanan e-commerce 
                        apotek kami. Dengan mengakses dan menggunakan website ini, Anda menyetujui untuk terikat dengan 
                        semua syarat dan ketentuan yang berlaku.
                    </p>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 my-4">
                        <p class="text-red-800 font-medium">
                            <strong>Penting:</strong> Layanan ini khusus untuk pembelian obat-obatan dan produk kesehatan. 
                            Penggunaan yang tidak sesuai dapat membahayakan kesehatan dan melanggar hukum.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Registration and Account -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Registrasi dan Akun</h2>
                <div class="space-y-4">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900 mb-3">Persyaratan Registrasi</h3>
                        <ul class="text-blue-800 space-y-2">
                            <li>• Berusia minimal 17 tahun atau memiliki persetujuan orang tua/wali</li>
                            <li>• Memberikan informasi yang akurat dan lengkap</li>
                            <li>• Memiliki alamat pengiriman yang valid di Indonesia</li>
                            <li>• Menyetujui verifikasi identitas jika diperlukan</li>
                        </ul>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-lg font-medium text-gray-900">Tanggung Jawab Akun</h3>
                        <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                            <li>Menjaga kerahasiaan password dan informasi login</li>
                            <li>Bertanggung jawab atas semua aktivitas yang terjadi di akun Anda</li>
                            <li>Segera melaporkan jika terjadi penggunaan akun yang tidak sah</li>
                            <li>Memperbarui informasi profil secara berkala</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Products and Services -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Produk dan Layanan</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="border border-green-200 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-900 mb-3">Obat Bebas</h3>
                        <ul class="text-green-800 space-y-2 text-sm">
                            <li>• Dapat dibeli tanpa resep dokter</li>
                            <li>• Tersedia informasi dosis dan cara pakai</li>
                            <li>• Konsultasi gratis dengan apoteker</li>
                            <li>• Garansi keaslian produk</li>
                        </ul>
                    </div>
                    <div class="border border-orange-200 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-orange-900 mb-3">Obat Keras & Resep</h3>
                        <ul class="text-orange-800 space-y-2 text-sm">
                            <li>• Wajib melampirkan resep dokter asli</li>
                            <li>• Verifikasi oleh apoteker berlisensi</li>
                            <li>• Resep akan disimpan sesuai regulasi</li>
                            <li>• Tidak dapat dibeli tanpa resep</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                    <h3 class="font-medium text-yellow-900 mb-2">Ketersediaan Stok</h3>
                    <p class="text-yellow-800 text-sm">
                        Stok produk dapat berubah sewaktu-waktu. Jika produk yang dipesan tidak tersedia, 
                        kami akan menghubungi Anda untuk penggantian atau pembatalan pesanan.
                    </p>
                </div>
            </section>

            <!-- Ordering and Payment -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Pemesanan dan Pembayaran</h2>
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-medium text-gray-900 mb-3">Proses Pemesanan</h3>
                        <div class="grid md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 text-sm font-bold">1</div>
                                <p class="text-sm font-medium text-blue-900">Pilih Produk atau Upload Resep</p>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 text-sm font-bold">2</div>
                                <p class="text-sm font-medium text-blue-900">Checkout</p>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 text-sm font-bold">3</div>
                                <p class="text-sm font-medium text-blue-900">Pembayaran</p>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 text-sm font-bold">4</div>
                                <p class="text-sm font-medium text-blue-900">Pesanan Diterima</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-medium text-gray-900 mb-3">Metode Pembayaran</h3>
                        <div class="space-y-4">
                            <div class="border border-blue-200 p-4 rounded-lg">
                                <h4 class="font-medium text-blue-900 mb-3">Transfer Bank</h4>
                                <div class="grid md:grid-cols-3 gap-2 text-sm text-blue-800">
                                    <span>• BCA (Bank Central Asia)</span>
                                    <span>• Mandiri (Bank Mandiri)</span>
                                    <span>• BNI (Bank Negara Indonesia)</span>
                                    <span>• BRI (Bank Rakyat Indonesia)</span>
                                    <span>• Permata Bank</span>
                                    <span>• CIMB Niaga</span>
                                    <span>• Danamon</span>
                                    <span>• BSI (Bank Syariah Indonesia)</span>
                                    <span>• Other Bank</span>
                                </div>
                            </div>
                            <div class="border border-green-200 p-4 rounded-lg">
                                <h4 class="font-medium text-green-900 mb-3">Kartu Kredit dan Kartu Debit</h4>
                                <div class="grid md:grid-cols-4 gap-2 text-sm text-green-800">
                                    <span>• VISA</span>
                                    <span>• MasterCard</span>
                                    <span>• JCB</span>
                                    <span>• American Express (Amex)</span>
                                </div>
                            </div>
                            <div class="border border-purple-200 p-4 rounded-lg">
                                <h4 class="font-medium text-purple-900 mb-3">QRIS</h4>
                                <div class="grid md:grid-cols-3 gap-2 text-sm text-purple-800">
                                    <span>• GoPay</span>
                                    <span>• ShopeePay</span>
                                    <span>• Dana</span>
                                    <span>• OVO</span>
                                    <span>• LinkAja</span>
                                    <span>• dan lainnya</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 p-4 rounded-lg">
                        <h3 class="font-medium text-red-900 mb-2">Batas Waktu Pembayaran</h3>
                        <p class="text-red-800 text-sm">
                            Pembayaran harus dilakukan dalam 24 jam setelah pemesanan. 
                            Pesanan akan otomatis dibatalkan jika pembayaran tidak diterima dalam batas waktu tersebut.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Delivery -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Pengiriman dan Penerimaan</h2>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg text-center">
                            <h3 class="font-medium text-green-900 mb-2">Pengiriman Lokal</h3>
                            <p class="text-sm text-green-800">Same day delivery oleh kurir internal</p>
                            @if($freeShippingMinimum > 0)
                                <p class="text-xs text-green-700 mt-1">Gratis ongkir min. Rp {{ number_format($freeShippingMinimum, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg text-center">
                            <h3 class="font-medium text-purple-900 mb-2">Ambil di Apotek</h3>
                            <p class="text-sm text-purple-800">Pickup langsung</p>
                            <p class="text-xs text-purple-700 mt-1">Siap dalam 2-4 jam</p>
                        </div>
                    </div>

                    <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                        <h3 class="font-medium text-yellow-900 mb-2">Penting untuk Penerimaan:</h3>
                        <ul class="text-yellow-800 text-sm space-y-1">
                            <li>• Periksa kondisi kemasan saat menerima</li>
                            <li>• Pastikan produk sesuai dengan pesanan</li>
                            <li>• Laporkan kerusakan atau ketidaksesuaian dalam 24 jam</li>
                            <li>• Simpan obat sesuai petunjuk penyimpanan</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Prescription Requirements -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Ketentuan Resep Dokter</h2>
                <div class="bg-red-50 border border-red-200 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-red-900 mb-4">Persyaratan Resep</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-red-900 mb-2">Format Resep</h4>
                            <ul class="text-red-800 text-sm space-y-1">
                                <li>• Resep asli dari dokter berlisensi</li>
                                <li>• Foto/scan dengan kualitas jelas</li>
                                <li>• Tanggal resep maksimal 30 hari</li>
                                <li>• Identitas pasien lengkap</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-red-900 mb-2">Verifikasi</h4>
                            <ul class="text-red-800 text-sm space-y-1">
                                <li>• Validasi oleh apoteker berlisensi</li>
                                <li>• Konfirmasi dengan dokter jika perlu</li>
                                <li>• Penolakan jika resep tidak valid</li>
                                <li>• Penyimpanan sesuai regulasi BPOM</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Returns and Exchanges -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Pengembalian dan Penukaran</h2>
                <div class="space-y-4">
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="font-medium text-yellow-900 mb-2">Kebijakan Khusus Obat-obatan</h3>
                        <p class="text-yellow-800 text-sm">
                            Mengingat sifat produk farmasi, pengembalian dan penukaran memiliki keterbatasan 
                            untuk menjaga keamanan dan kualitas produk.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="border border-green-200 p-4 rounded-lg">
                            <h3 class="font-medium text-green-900 mb-3">Dapat Dikembalikan</h3>
                            <ul class="text-green-800 text-sm space-y-1">
                                <li>• Produk rusak/cacat dari pabrik</li>
                                <li>• Kesalahan pengiriman produk</li>
                                <li>• Produk kadaluarsa saat diterima</li>
                                <li>• Kemasan tidak tersegel dengan baik</li>
                            </ul>
                        </div>
                        <div class="border border-red-200 p-4 rounded-lg">
                            <h3 class="font-medium text-red-900 mb-3">Tidak Dapat Dikembalikan</h3>
                            <ul class="text-red-800 text-sm space-y-1">
                                <li>• Obat yang sudah dibuka/digunakan</li>
                                <li>• Perubahan pikiran pembeli</li>
                                <li>• Produk custom/racikan</li>
                                <li>• Setelah 7 hari dari penerimaan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Prohibitions -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Larangan dan Pembatasan</h2>
                <div class="bg-red-50 border-l-4 border-red-500 p-6">
                    <h3 class="text-lg font-medium text-red-900 mb-4">Dilarang Keras:</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <ul class="text-red-800 space-y-2">
                            <li>❌ Menjual kembali obat yang dibeli</li>
                            <li>❌ Menggunakan resep palsu atau milik orang lain</li>
                            <li>❌ Membeli untuk tujuan penyalahgunaan</li>
                            <li>❌ Memberikan informasi palsu</li>
                        </ul>
                        <ul class="text-red-800 space-y-2">
                            <li>❌ Mengakses akun orang lain</li>
                            <li>❌ Melakukan aktivitas ilegal</li>
                            <li>❌ Mengganggu sistem website</li>
                            <li>❌ Menyebarkan malware atau virus</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Liability -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Tanggung Jawab dan Batasan</h2>
                <div class="space-y-4">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900 mb-3">Tanggung Jawab Apotek Baraya</h3>
                        <ul class="text-blue-800 space-y-2">
                            <li>• Menyediakan obat asli dan berkualitas</li>
                            <li>• Verifikasi resep oleh apoteker berlisensi</li>
                            <li>• Penyimpanan dan pengiriman yang tepat</li>
                            <li>• Perlindungan data pribadi pelanggan</li>
                        </ul>
                    </div>

                    <div class="bg-orange-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-orange-900 mb-3">Tanggung Jawab Pelanggan</h3>
                        <ul class="text-orange-800 space-y-2">
                            <li>• Menggunakan obat sesuai petunjuk dokter</li>
                            <li>• Menyimpan obat dengan benar</li>
                            <li>• Melaporkan efek samping yang terjadi</li>
                            <li>• Tidak menyalahgunakan obat</li>
                        </ul>
                    </div>

                    <div class="border border-gray-300 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-2">Batasan Tanggung Jawab</h3>
                        <p class="text-gray-700 text-sm">
                            Apotek Baraya tidak bertanggung jawab atas efek samping obat yang timbul akibat 
                            penggunaan yang tidak sesuai petunjuk dokter atau penyalahgunaan obat oleh pelanggan.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Dispute Resolution -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Penyelesaian Sengketa</h2>
                <div class="space-y-4">
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-900 mb-3">Langkah Penyelesaian</h3>
                        <ol class="text-green-800 space-y-2">
                            <li><strong>1. Komunikasi Langsung:</strong> Hubungi customer service kami terlebih dahulu</li>
                            <li><strong>2. Mediasi Internal:</strong> Tim khusus akan membantu menyelesaikan masalah</li>
                            <li><strong>3. Mediasi Eksternal:</strong> Melalui BPSK atau lembaga mediasi resmi</li>
                            <li><strong>4. Jalur Hukum:</strong> Pengadilan yang berwenang di Jakarta Pusat</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- Changes to Terms -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Perubahan Syarat & Ketentuan</h2>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-gray-700 mb-4">
                        Kami berhak mengubah Syarat & Ketentuan ini sewaktu-waktu. Perubahan akan diberitahukan melalui:
                    </p>
                    <ul class="text-gray-700 space-y-2 mb-4">
                        <li>• Notifikasi di website</li>
                        <li>• Email ke alamat terdaftar</li>
                        <li>• Pesan di aplikasi mobile (jika ada)</li>
                    </ul>
                    <p class="text-gray-700 text-sm">
                        Penggunaan layanan setelah perubahan dianggap sebagai persetujuan terhadap syarat yang baru.
                    </p>
                </div>
            </section>

            <!-- Contact Information -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Informasi Kontak</h2>
                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-blue-900 mb-3">Customer Service</h3>
                            <div class="text-blue-800 space-y-1 text-sm">
                                <p>📧 Email: {{ $storeEmail }}</p>
                                <p>📞 Telepon: {{ $storePhone }}</p>
                                @if($storeWhatsapp)
                                    <p>💬 WhatsApp: {{ $storeWhatsapp }}</p>
                                @endif
                                <p>🕒 Jam Operasional: 08.00 - 22.00 WIB</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-medium text-blue-900 mb-3">{{ $storeName }}</h3>
                            <div class="text-blue-800 space-y-1 text-sm">
                                <p>📍 {{ $this->fullAddress }}</p>
                                @if($storeSipa)
                                    <p>🏥 SIPA: {{ $storeSipa }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <section class="border-t pt-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-2">
                        Dengan menggunakan layanan Apotek Baraya, Anda menyetujui Syarat & Ketentuan ini.
                    </p>
                    <p class="text-xs text-gray-400">
                        Terakhir diperbarui: {{ date('d F Y') }} | Berlaku sejak: {{ date('d F Y') }}
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>