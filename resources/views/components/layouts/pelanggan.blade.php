<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'User' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="min-h-screen bg-base-200">
    <nav class="sticky top-0 z-50 bg-gray-100 shadow-sm ">
        <div class="container mx-auto navbar">
            <div class="flex-1">
                <a class="text-xl font-bold text-green-500" href="/pelanggan/dashboard">Apotek Baraya</a>
            </div>
            <div class="flex gap-5">
                <input type="text" placeholder="Pencarian" class="w-24 input input-success input-bordered md:w-auto" />
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full ring-success ring-offset-base-100 ring-2 ring-offset-2">
                            <img
                                alt="Tailwind CSS Navbar component"
                                src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                        </div>
                    </div>
                    <ul
                        tabindex="0"
                        class="p-2 mt-3 shadow menu menu-sm dropdown-content bg-base-100 rounded-box z-1 w-52">
                        <li>
                            <a class="justify-between">
                                Profil
                                <span class="badge">Baru</span>
                            </a>
                        </li>
                        <li><a>Pengaturan</a></li>
                        <li><a>Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>


    <div class="container mx-auto ">
        {{ $slot }}
    </div>


    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>