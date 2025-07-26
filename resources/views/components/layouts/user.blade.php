<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'User' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="min-h-screen bg-base-200">
    <nav class="sticky top-0 z-50 bg-gray-100 shadow-sm">
        <div class="container mx-auto navbar">
            <div class="flex-1">
                <a class="text-xl font-bold text-green-500" href="/">Apotek Baraya</a>
            </div>
            <div class="flex gap-5">
                <input type="text" placeholder="Pencarian" class="w-24 input input-success input-bordered md:w-auto" />
                <div class="flex gap-2">
                    <a class="btn btn-success" href="/login">Registrasi</a>
                    <a class="btn btn-outline btn-success" href="/register">Login</a>
                </div>
            </div>
        </div>
    </nav>


    <div class="flex items-center justify-center min-h-screen ">
        {{ $slot }}
    </div>


    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>