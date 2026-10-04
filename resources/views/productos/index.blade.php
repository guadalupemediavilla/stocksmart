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
                    <div class="flex items-center gap-3">
                        @if ($producto->fotos->isNotEmpty())
                            <img src="{{ asset('storage/' . $producto->fotos->first()->ruta) }}" alt=""
                                 data-ampliable
                                 data-fotos='@json($producto->fotos->map(fn($f) => asset("storage/" . $f->ruta)))'
                                 class="h-10 w-10 shrink-0 cursor-zoom-in rounded-md border border-[#E2E8F0] object-cover">
                        @else
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-dashed border-[#E2E8F0] bg-slate-50 text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21zM14.25 8.25h.008v.008h-.008V8.25z" />
                                </svg>
                            </div>
                        @endif
                        <p class="font-medium text-[#0F172A]">{{ $producto->nombre }}</p>
                    </div>

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
                              data-confirmar="¿Seguro de que querés borrar este producto?">
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
                            <td class="px-6 py-3 font-medium text-[#0F172A]">
                                <div class="flex items-center gap-3">
                                    @if ($producto->fotos->isNotEmpty())
                                        <img src="{{ asset('storage/' . $producto->fotos->first()->ruta) }}" alt=""
                                             data-ampliable
                                             data-fotos='@json($producto->fotos->map(fn($f) => asset("storage/" . $f->ruta)))'
                                             class="h-10 w-10 shrink-0 cursor-zoom-in rounded-md border border-[#E2E8F0] object-cover">
                                    @else
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md border border-dashed border-[#E2E8F0] bg-slate-50 text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21zM14.25 8.25h.008v.008h-.008V8.25z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <span>{{ $producto->nombre }}</span>
                                </div>
                            </td>
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
                                          data-confirmar="¿Seguro de que querés borrar este producto?">
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