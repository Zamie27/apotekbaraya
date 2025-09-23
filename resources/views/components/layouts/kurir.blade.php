@props(['title' => 'Dashboard Kurir - Apotek Baraya'])

@php
use App\Models\StoreSetting;
@endphp

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/src/img/logo.png">
    <title>{{ $title ?? 'Apotek Baraya' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="min-h-screen bg-base-200">
    <nav class="sticky top-0 z-50 bg-gray-100 shadow-lg">
        <div class="container flex justify-between mx-auto navbar">
            <!-- Logo Section -->
            <div class="flex items-center justify-between gap-2 text-2xl font-bold">
                <img src="/src/img/logo.png" alt="Logo" class="w-auto h-8 max-h-8">
                <a class="text-xl font-bold text-green-500" href="/">Apotek Baraya</a>

            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                @auth
                <!-- Notification Bell (only for logged in users) -->
                <button class="p-2 btn btn-circle hover:bg-gray-200">
                    <x-icons.bell class="text-gray-700" />
                </button>

                <!-- Mail Icon (only for logged in users) -->
                <button class="p-2 btn btn-circle hover:bg-gray-200">
                    <x-icons.mail class="text-gray-700" />
                </button>
                @endauth

                <!-- Profile Dropdown (for logged in users) -->
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full ring-success ring-offset-base-100 ring-2 ring-offset-2">
                            <img
                                alt="Profile Picture"
                                src="{{ auth()->user()->getAvatarUrl() }}" />
                        </div>
                    </div>
                    <ul
                        tabindex="0"
                        class="p-2 mt-3 shadow menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] w-52">
                        <li><a href="{{ route('kurir.deliveries') }}">Manajemen Pengiriman</a></li>
                        <li><a href="/kurir/profile">Profil</a></li>
                        <li><a href="/logout" class="text-left text-red-600 hover:bg-base-200">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto">
        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-4 mb-4 alert alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-4 mb-4 alert alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{ $slot }}
    </div>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="lg:col-span-2">
                    <div class="flex items-start">
                        <!-- Logo -->
                        <img src="/src/img/logo.png" alt="Apotek Baraya Logo" class="h-10 sm:h-12 me-2 sm:me-3">

                        <!-- Company Details -->
                        <div class="ml-10 sm:ml-13">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">{{ StoreSetting::get('store_name', 'Apotek Baraya') }}</h3>

                            <!-- Contact Information -->
                            <div class="text-xs sm:text-sm text-gray-600 space-y-1 font-medium">
                                <!-- Address -->
                                <div class="leading-relaxed">
                                    @php
                                    $storeAddress = config('app.store_address', 'Jl. Raya Apotek No. 123');
                                    $storeVillage = config('app.store_village', 'Sukamandi');
                                    $storeDistrict = config('app.store_district', 'Ciasem');
                                    $storeRegency = config('app.store_regency', 'Subang');
                                    $storeProvince = config('app.store_province', 'Jawa Barat');
                                    $storePostalCode = config('app.store_postal_code', '41256');
                                    @endphp
                                    <div>{{ $storeAddress }}</div>
                                    <div>{{ $storeVillage }}, {{ $storeDistrict }}, {{ $storeRegency }}</div>
                                    <div>{{ $storeProvince }}, {{ $storePostalCode }}</div>
                                </div>

                                <!-- Phone -->
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ StoreSetting::get('store_phone', '+62 812-3456-7890') }}
                                </div>

                                <!-- Email -->
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ StoreSetting::get('store_email', 'info@apotekbaraya.com') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Legal -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li>
                            <a href="{{ route('privacy-policy') }}" class="text-gray-600 hover:text-primary transition-colors font-medium">Kebijakan Privasi</a>
                        </li>
                        <li>
                            <a href="{{ route('terms-conditions') }}" class="text-gray-600 hover:text-primary transition-colors font-medium">Syarat &amp; Ketentuan</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="border-t border-gray-200 mt-8 pt-6">
                <div class="text-center space-y-2">
                    <p class="text-xs sm:text-sm text-gray-600 font-medium">
                        © 2025 Apotek Baraya™. All Rights Reserved.
                    </p>
                    <p class="text-xs sm:text-sm text-gray-500">
                        Dibuat dengan 💖 oleh <a href="https://kuukok.my.id" target="_blank" class="text-primary hover:text-primary-focus transition-colors font-medium">Kuukok™</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Auto Refresh Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for auto refresh event from OrderStatusActions component
            Livewire.on('auto-refresh-page', (data) => {
                const delay = data[0]?.delay || 2000; // Default 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, delay);
            });
        });
    </script>

</body>

</html>