<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'StockSmart')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8FAFC] font-sans text-[#1E293B] antialiased">
    @include('partials.navbar')

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-10 xl:px-16">
        @yield('content')
    </main>

    @include('partials.lightbox')
    @include('partials.confirmar')
</body>
</html>