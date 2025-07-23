<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Admin' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="bg-gray-100">
    <nav class="p-4 text-white bg-blue-500">
        Dashboard Admin Navbar
    </nav>

    <main class="p-6">
        {{ $slot }}
    </main>

    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>