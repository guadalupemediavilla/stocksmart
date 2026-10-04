@extends('layouts.app')

@section('title', 'Categorías · StockSmart')

@section('content')
    <a href="/home" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver al inicio</a>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">Categorías</h1>

        <a href="/categorias/create"
           class="inline-flex items-center justify-center rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
            Agregar categoría
        </a>
    </div>

    @if (session('error'))
        <div class="mt-6 rounded-lg border border-[#D97706] bg-[#D97706]/10 px-4 py-3 text-sm text-[#D97706]">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="/categorias" class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-4">
            <div class="flex-1">
                <label class="block text-xs font-medium text-slate-500">Buscar por nombre</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Ej: ropa"
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="rounded-md bg-[#0F172A] px-4 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                    Filtrar
                </button>
                @if (request()->filled('buscar'))
                    <a href="/categorias" class="text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">Limpiar</a>
                @endif
            </div>
        </div>
    </form>

    @if ($categorias->isEmpty())
        <div class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
            @if (request()->filled('buscar'))
                No se encontraron categorías con ese nombre.
            @else
                Todavía no hay categorías cargadas.
            @endif
        </div>
    @else
        <div class="mt-6 divide-y divide-[#E2E8F0] overflow-hidden rounded-lg border border-[#E2E8F0] bg-white shadow-sm">
            @foreach ($categorias as $categoria)
                <div class="flex flex-col gap-2 px-4 py-4 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <p class="font-medium text-[#0F172A]">{{ $categoria->nombre }}</p>

                    <div class="flex items-center gap-4 text-sm">
                        <a href="/categorias/{{ $categoria->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Editar</a>

                        <form method="POST" action="/categorias/{{ $categoria->id }}"
                              onsubmit="return confirm('¿Seguro de que querés borrar esta categoría?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-slate-500 hover:text-slate-700 hover:underline">
                                Borrar
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection