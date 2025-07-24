<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'User' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="min-h-screen bg-base-200">
    <nav class="sticky top-0 shadow-sm navbar bg-base-100">
        <div class="flex-1">
            <a class="text-xl btn btn-ghost">daisyUI</a>
        </div>
        <div class="flex gap-2">
            <input type="text" placeholder="Search" class="w-24 input input-bordered md:w-auto" />
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
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
                            Profile
                            <span class="badge">New</span>
                        </a>
                    </li>
                    <li><a>Settings</a></li>
                    <li><a>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="flex items-center justify-center min-h-screen">
        {{ $slot }}
    </div>


    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>