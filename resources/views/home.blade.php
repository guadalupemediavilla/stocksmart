@extends('layouts.app')

@section('title', 'Inicio · StockSmart')

@section('content')
    <div class="mx-auto max-w-5xl lg:flex lg:min-h-[60vh] lg:flex-col lg:justify-center lg:py-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-[#0F172A] lg:text-3xl">
                Hola, {{ Auth::user()->nombre }}
            </h1>

            @if ($dolar)
                <div class="rounded-lg border border-[#E2E8F0] bg-white px-4 py-3 text-sm shadow-sm">
                    <span class="font-medium text-[#0F172A]">Dólar oficial</span>
                    <span class="text-[#1E293B]">
                        — compra ${{ $dolar['compra'] }} · venta ${{ $dolar['venta'] }}
                    </span>
                </div>
            @endif
        </div>

        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:mt-14 lg:grid-cols-4 lg:gap-8">

            <a href="/productos" class="group rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm transition-colors hover:border-[#0D9488] lg:p-8">
                <svg class="h-9 w-9 text-[#0D9488] lg:h-11 lg:w-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375C2.754 3.75 2.25 4.254 2.25 4.875v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                <h3 class="mt-4 font-semibold text-[#0F172A] group-hover:text-[#0D9488] lg:mt-5 lg:text-lg">Productos</h3>
                <p class="mt-1 text-sm text-slate-500">Gestioná el catálogo y las variantes</p>
            </a>

            <a href="/categorias" class="group rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm transition-colors hover:border-[#0D9488] lg:p-8">
                <svg class="h-9 w-9 text-[#0D9488] lg:h-11 lg:w-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                </svg>
                <h3 class="mt-4 font-semibold text-[#0F172A] group-hover:text-[#0D9488] lg:mt-5 lg:text-lg">Categorías</h3>
                <p class="mt-1 text-sm text-slate-500">Organizá los productos por categoría</p>
            </a>

            @if (Auth::user()->rol === 'admin')
                <a href="/empleados" class="group rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm transition-colors hover:border-[#0D9488] lg:p-8">
                    <svg class="h-9 w-9 text-[#0D9488] lg:h-11 lg:w-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    <h3 class="mt-4 font-semibold text-[#0F172A] group-hover:text-[#0D9488] lg:mt-5 lg:text-lg">Empleados</h3>
                    <p class="mt-1 text-sm text-slate-500">Administrá el equipo</p>
                </a>

                <a href="/dashboard" class="group rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm transition-colors hover:border-[#0D9488] lg:p-8">
                    <svg class="h-9 w-9 text-[#0D9488] lg:h-11 lg:w-11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <h3 class="mt-4 font-semibold text-[#0F172A] group-hover:text-[#0D9488] lg:mt-5 lg:text-lg">Panel</h3>
                    <p class="mt-1 text-sm text-slate-500">Métricas e indicadores del inventario</p>
                </a>
            @endif

        </div>
    </div>
@endsection
