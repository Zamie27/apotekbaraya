@props(['title' => 'Apotek Baraya'])

@php
use App\Models\StoreSetting;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/src/img/logo.png">
    <title>{{ $title }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>

    @stack('head')
</head>

<body class="min-h-screen bg-base-200">
    <nav class="sticky top-0 z-50 bg-gray-100 shadow-lg">
        <div class="container mx-auto px-3 sm:px-6">
            <div class="navbar min-h-16 sm:min-h-20">
                <!-- Logo Section -->
                <div class="navbar-start">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <img src="/src/img/logo.png" alt="Logo" class="w-8 h-8 sm:w-10 sm:h-10">
                        <a class="text-lg sm:text-xl lg:text-2xl font-bold text-green-500" href="/">Apotek Baraya</a>
                    </div>
                </div>

                <!-- Center - Search (Hidden on mobile, shown in dropdown) -->
                <div class="navbar-center hidden lg:flex justify-center">
                    <div class="form-control w-full max-w-xs xl:max-w-sm">
                        @livewire('navbar-search')
                    </div>
                </div>

                <!-- Right Section -->
                <div class="navbar-end">
                    <div class="flex items-center gap-2 sm:gap-3">
                        @auth
                        <!-- Mobile Search Button -->
                        <div class="dropdown dropdown-end lg:hidden">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle btn-md sm:btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div tabindex="0" class="dropdown-content z-[1] p-3 sm:p-4 shadow bg-base-100 rounded-box w-72 sm:w-80 mt-3">
                                @livewire('navbar-search')
                            </div>
                        </div>

                        <!-- My Orders Button (hidden on mobile) -->
                        <a href="{{ route('user.orders') }}" class="btn btn-ghost btn-circle btn-md sm:btn-lg hidden sm:flex relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            @livewire('order-counter')
                        </a>

                        <!-- Cart Icon with Counter -->
                        <a href="{{ route('cart') }}" class="btn btn-ghost btn-circle btn-md sm:btn-lg relative">
                            <x-icons.shopping-cart class="h-5 w-5 sm:h-6 sm:w-6 text-gray-700" />
                            <span id="cart-counter" class="absolute -top-1 -right-1 sm:-top-1.5 sm:-right-1.5 bg-red-500 text-white text-xs rounded-full h-5 w-5 sm:h-6 sm:w-6 flex items-center justify-center hidden">
                                0
                            </span>
                        </a>

                        <!-- Profile Dropdown (for logged in users) -->
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle btn-md sm:btn-lg avatar">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 rounded-full ring-success ring-offset-base-100 ring-2 sm:ring-2 ring-offset-2">
                                    <img alt="Profile Picture" src="{{ auth()->user()->getAvatarUrl() }}" />
                                </div>
                            </div>
                            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                                <li><a href="/profile" class="text-sm">Profil</a></li>
                                <li><a href="/logout" class="text-red-600 hover:bg-base-200 text-sm">Keluar</a></li>
                            </ul>
                        </div>
                        @else
                        <!-- Mobile Search Button for guests -->
                        <div class="dropdown dropdown-end lg:hidden">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle btn-md sm:btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div tabindex="0" class="dropdown-content z-[1] p-3 sm:p-4 shadow bg-base-100 rounded-box w-72 sm:w-80 mt-3">
                                @livewire('navbar-search')
                            </div>
                        </div>

                        <!-- Login/Register Buttons (for guests) -->
                        <div class="flex gap-2 sm:gap-3">
                            <a class="btn btn-success btn-sm sm:btn-md text-sm sm:text-base px-3 sm:px-4" href="/register">Registrasi</a>
                            <a class="btn btn-outline btn-success btn-sm sm:btn-md text-sm sm:text-base px-3 sm:px-4" href="/login">Login</a>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-2 sm:px-4 lg:px-6">
        {{ $slot }}
    </div>

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

    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireScripts

    @stack('scripts')

    <script>
        // Update cart counter (only for authenticated users)
        function updateCartCounter() {
            @auth
            const counter = document.getElementById('cart-counter');
            if (counter) {
                fetch('/api/cart/count')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            counter.textContent = data.count;
                            counter.classList.remove('hidden');
                        } else {
                            counter.classList.add('hidden');
                        }
                    })
                    .catch(error => console.error('Error updating cart counter:', error));
            }
            @endauth
        }

        // Update counter on page load (only for authenticated users)
        document.addEventListener('DOMContentLoaded', function() {
            @auth
            updateCartCounter();
            @endauth
        });

        // Listen for Livewire events (only for authenticated users)
        document.addEventListener('livewire:init', () => {
            @auth
            Livewire.on('cart-updated', () => {
                updateCartCounter();
            });
            @endauth
        });

        // Listen for auto refresh event from OrderStatusActions component
        Livewire.on('auto-refresh-page', (data) => {
            const delay = data[0]?.delay || 2000; // Default 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, delay);
        });
    </script>

    <!-- Confirmation Modal -->
    @livewire('confirmation-modal')

    <!-- Toast Notifications -->
    @livewire('toast-notification')

    @livewireScripts
</body>

</html>