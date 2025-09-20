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
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 ">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 ">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <a href="/" class="flex ms-2 md:me-24">
                        <img src="/src/img/logo.png" class="h-8 me-3" alt="FlowBite Logo" />
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap ">Apotek Baraya</span>
                    </a>
                </div>
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
                        <li><a href="/admin/profile">Profil</a></li>
                        <li><a href="/logout" class="text-left text-red-600 hover:bg-base-200">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 " aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white ">
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100' : '' }}">
                        <x-icons.pie-chart class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" />
                        <span class="ms-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.orders*') ? 'bg-gray-100' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="ms-3">Manajemen Pesanan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.refunds') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.refunds*') ? 'bg-gray-100' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="ms-3">Manajemen Refund</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.users*') ? 'bg-gray-100' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span class="ms-3">Manajemen Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.email-notifications') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.email-notifications*') ? 'bg-gray-100' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="ms-3">Notifikasi Email</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.user-activity-reports') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.user-activity-reports*') ? 'bg-gray-100' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                        </svg>
                        <span class="ms-3">Laporan Aktivitas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group {{ request()->routeIs('admin.settings') ? 'bg-gray-100' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 shrink-0 group-hover:text-gray-900" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ms-3">Pengaturan Toko</span>
                    </a>
                </li>


            </ul>
        </div>
    </aside>

    <main class="p-6 pt-20 sm:ml-64">
        {{ $slot }}
    </main>

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