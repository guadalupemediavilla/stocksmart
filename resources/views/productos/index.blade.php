@extends('layouts.app')

@section('title', 'Productos · StockSmart')

@section('content')
<a href="/home" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver al inicio</a>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">Productos</h1>

        <a href="/productos/create"
           class="inline-flex items-center justify-center rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
            Agregar producto
        </a>
    </div>

    <form method="GET" action="/productos" class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-slate-500">Buscar por nombre</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Ej: remera"
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Buscar por atributo</label>
                <input type="text" name="atributo" value="{{ request('atributo') }}" placeholder="Ej: negro, M, A4"
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>
        </div>

        @if ($categorias->isNotEmpty())
            <div class="mt-4">
                <label class="block text-xs font-medium text-slate-500">Categorías</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($categorias as $categoria)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}" class="peer hidden"
                                   {{ in_array($categoria->id, request('categorias', [])) ? 'checked' : '' }}>
                            <span class="inline-block rounded-full border border-[#E2E8F0] bg-slate-50 px-3 py-1 text-xs font-medium text-slate-600 transition-colors peer-checked:border-[#0D9488] peer-checked:bg-[#0D9488] peer-checked:text-white">
                                {{ $categoria->nombre }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-4 flex items-center gap-4">
            <button type="submit"
                    class="rounded-md bg-[#0F172A] px-4 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                Filtrar
            </button>
            @if (request()->anyFilled(['buscar', 'atributo', 'categorias']))
                <a href="/productos" class="text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">Limpiar filtros</a>
            @endif
        </div>
    </form>

    @if ($productos->isEmpty())
        <div class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
            @if (request()->anyFilled(['buscar', 'atributo', 'categorias']))
                No se encontraron productos con esos filtros.
            @else
                Todavía no hay productos cargados.
            @endif
        </div>
    @else
        {{-- Mobile: tarjetas apiladas --}}
        <div class="mt-6 flex flex-col gap-3 sm:hidden">
            @foreach ($productos as $producto)
                <div class="rounded-lg border border-[#E2E8F0] bg-white p-4 shadow-sm">
                    <p class="font-medium text-[#0F172A]">{{ $producto->nombre }}</p>

                    @if ($producto->categorias->isNotEmpty())
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($producto->categorias as $categoria)
                                <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600">
                                    {{ $categoria->nombre }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <a href="/productos/{{ $producto->id }}" class="font-medium text-[#0D9488] hover:underline">Ver</a>
                        <a href="/productos/{{ $producto->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Editar</a>

                        <form method="POST" action="/productos/{{ $producto->id }}"
                              onsubmit="return confirm('¿Seguro de que querés borrar este producto?')">
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

        {{-- Tablet/desktop: tabla --}}
        <div class="mt-6 hidden overflow-hidden rounded-lg border border-[#E2E8F0] bg-white shadow-sm sm:block">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-[#E2E8F0] bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3">Categorías</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @foreach ($productos as $producto)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-medium text-[#0F172A]">{{ $producto->nombre }}</td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($producto->categorias as $categoria)
                                        <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2 py-0.5 text-xs font-medium text-slate-600">
                                            {{ $categoria->nombre }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap items-center justify-end gap-x-4 gap-y-1">
                                    <a href="/productos/{{ $producto->id }}" class="font-medium text-[#0D9488] hover:underline">Ver</a>
                                    <a href="/productos/{{ $producto->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Editar</a>

                                    <form method="POST" action="/productos/{{ $producto->id }}"
                                          onsubmit="return confirm('¿Seguro de que querés borrar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-slate-500 hover:text-slate-700 hover:underline">
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection