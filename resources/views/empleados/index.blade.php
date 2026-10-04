@extends('layouts.app')

@section('title', 'Empleados · StockSmart')

@section('content')
<a href="/home" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver al inicio</a>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">Empleados</h1>

        <a href="/empleados/create"
           class="inline-flex items-center justify-center rounded-md bg-[#0F172A] px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
            Agregar empleado
        </a>
    </div>

    <form method="GET" action="/empleados" class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-[1fr_auto]">
            <div>
                <label class="block text-xs font-medium text-slate-500">Buscar por nombre o apellido</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Ej: guadalupe"
                       class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Rol</label>
                <select name="rol"
                        class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488] sm:w-40">
                    <option value="">Todos</option>
                    <option value="admin" {{ request('rol') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="empleado" {{ request('rol') === 'empleado' ? 'selected' : '' }}>Empleado</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <button type="submit"
                    class="rounded-md bg-[#0F172A] px-4 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                Filtrar
            </button>
            @if (request()->anyFilled(['buscar', 'rol']))
                <a href="/empleados" class="text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">Limpiar filtros</a>
            @endif
        </div>
    </form>

    @if ($empleados->isEmpty())
        <div class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
            @if (request()->anyFilled(['buscar', 'rol']))
                No se encontraron empleados con esos filtros.
            @else
                Todavía no hay empleados cargados.
            @endif
        </div>
    @else
        {{-- Mobile: tarjetas apiladas --}}
        <div class="mt-6 flex flex-col gap-3 sm:hidden">
            @foreach ($empleados as $empleado)
                <div class="rounded-lg border border-[#E2E8F0] bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium text-[#0F172A]">{{ $empleado->nombre }} {{ $empleado->apellido }}</p>
                        @if ($empleado->rol === 'admin')
                            <span class="shrink-0 rounded-full bg-[#0F172A] px-2.5 py-0.5 text-xs font-medium text-white">Admin</span>
                        @else
                            <span class="shrink-0 rounded-full border border-[#E2E8F0] bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ ucfirst($empleado->rol) }}</span>
                        @endif
                    </div>

                    <p class="mt-1 break-words text-sm text-slate-500">{{ $empleado->mail }}</p>

                    <div class="mt-3 flex items-center gap-4 text-sm">
                        <a href="/empleados/{{ $empleado->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Editar</a>

                        <form method="POST" action="/empleados/{{ $empleado->id }}"
                              onsubmit="return confirm('¿Seguro de que querés borrar a este empleado?')">
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
                        <th class="px-6 py-3">Mail</th>
                        <th class="px-6 py-3">Rol</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @foreach ($empleados as $empleado)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-medium text-[#0F172A]">{{ $empleado->nombre }} {{ $empleado->apellido }}</td>
                            <td class="break-words px-6 py-3 text-slate-500">{{ $empleado->mail }}</td>
                            <td class="px-6 py-3">
                                @if ($empleado->rol === 'admin')
                                    <span class="rounded-full bg-[#0F172A] px-2.5 py-0.5 text-xs font-medium text-white">Admin</span>
                                @else
                                    <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ ucfirst($empleado->rol) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="/empleados/{{ $empleado->id }}/edit" class="font-medium text-[#0D9488] hover:underline">Editar</a>

                                    <form method="POST" action="/empleados/{{ $empleado->id }}"
                                          onsubmit="return confirm('¿Seguro de que querés borrar a este empleado?')">
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