<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'StockSmart')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8FAFC] font-sans text-[#1E293B] antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-lg flex-col justify-center px-4 py-12 sm:px-6">
        @yield('content')
    </main>
</body>
</html>
