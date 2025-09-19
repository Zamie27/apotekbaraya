<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Email Anda - Apotek Baraya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-emerald-50 to-teal-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-emerald-500 rounded-full flex items-center justify-center mb-6">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Cek Email Anda</h2>
                <p class="text-gray-600">Aktivasi akun diperlukan untuk melanjutkan</p>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Success/Error Messages -->
                @if (session('success'))
                    <div class="alert alert-success mb-6">
                        <svg class="w-6 h-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error mb-6">
                        <svg class="w-6 h-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info mb-6">
                        <svg class="w-6 h-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                <!-- Main Message -->
                <div class="text-center mb-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-blue-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Email Aktivasi Telah Dikirim!</h3>
                        <p class="text-blue-700 text-sm leading-relaxed">
                            Kami telah mengirimkan email aktivasi ke alamat email yang Anda daftarkan. 
                            Silakan cek inbox (dan folder spam) Anda, lalu klik link aktivasi untuk mengaktifkan akun.
                        </p>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Langkah selanjutnya:</h4>
                        <ol class="text-sm text-gray-600 space-y-2 text-left">
                            <li class="flex items-start">
                                <span class="bg-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                                Buka aplikasi email Anda
                            </li>
                            <li class="flex items-start">
                                <span class="bg-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                                Cari email dari "Apotek Baraya" dengan subjek "Aktivasi Akun"
                            </li>
                            <li class="flex items-start">
                                <span class="bg-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                                Klik tombol "Aktivasi Akun Saya" di dalam email
                            </li>
                            <li class="flex items-start">
                                <span class="bg-emerald-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">4</span>
                                Anda akan diarahkan kembali ke halaman login
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- Resend Form -->
                <div class="border-t pt-6">
                    <p class="text-sm text-gray-600 text-center mb-4">Tidak menerima email?</p>
                    
                    <form action="{{ route('email.verification.resend') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Anda</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   required 
                                   class="input input-bordered w-full" 
                                   placeholder="Masukkan email yang didaftarkan"
                                   value="{{ old('email') }}">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Kirim Ulang Email Aktivasi
                        </button>
                    </form>
                </div>

                <!-- Back to Login -->
                <div class="text-center mt-6 pt-6 border-t">
                    <p class="text-sm text-gray-600 mb-3">Sudah mengaktifkan akun?</p>
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Kembali ke Login
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    Butuh bantuan? Hubungi kami di 
                    <a href="mailto:support@apotekbaraya.com" class="text-emerald-600 hover:text-emerald-700">support@apotekbaraya.com</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>