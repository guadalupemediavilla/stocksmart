@extends('layouts.app')

@section('title', 'Movimientos · ' . $variante->producto->nombre . ' · StockSmart')

@section('content')
    @php
        $atributos = json_decode($variante->atributos, true) ?? [];
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
    @endphp

    <a href="/productos/{{ $variante->producto->id }}" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver a {{ $variante->producto->nombre }}</a>

    <div class="mt-4">
        <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">{{ $variante->producto->nombre }}</h1>
        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span class="font-medium text-[#0F172A]">SKU: {{ $variante->sku }}</span>
            @foreach ($atributos as $clave => $valor)
                <span class="rounded-full border border-[#E2E8F0] bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">
                    {{ $clave }}: {{ $valor }}
                </span>
            @endforeach
        </div>
    </div>

    @if (session('error'))
        <div class="mt-6 rounded-lg border border-[#D97706] bg-[#D97706]/10 px-4 py-3 text-sm text-[#D97706]">
            {{ session('error') }}
        </div>
    @endif

    {{-- ===================== FILTROS ===================== --}}
    <form method="GET" action="/variantes/{{ $variante->id }}/movimientos" class="mt-6 rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-slate-500">Mes</label>
                <select name="mes" class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                    @foreach ($meses as $numero => $nombre)
                        <option value="{{ $numero }}" {{ $mes == $numero ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Año</label>
                <select name="anio" class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                    @for ($a = $anioMaximo; $a >= $anioMinimo; $a--)
                        <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500">Día</label>
                <select name="dia" class="mt-1 w-full rounded-md border border-[#E2E8F0] px-3 py-1.5 text-sm focus:border-[#0D9488] focus:outline-none focus:ring-1 focus:ring-[#0D9488]">
                    <option value="">Todo el mes</option>
                    @for ($d = 1; $d <= $diasEnMes; $d++)
                        <option value="{{ $d }}" {{ $dia == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <button type="submit"
                    class="rounded-md bg-[#0F172A] px-4 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-[#0F172A]/90">
                Filtrar
            </button>
            @if ($dia)
                <a href="/variantes/{{ $variante->id }}/movimientos" class="text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">Limpiar filtros</a>
            @endif
        </div>
    </form>

    {{-- ===================== RESUMEN DEL PERÍODO ===================== --}}
    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Stock al inicio</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#0F172A]">{{ $stockInicio }}</p>
        </div>
        <div class="rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Entradas</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#0F172A]">{{ $entradasPeriodo }}</p>
        </div>
        <div class="rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Salidas</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#0F172A]">{{ $salidasPeriodo }}</p>
        </div>
        <div class="rounded-lg border border-[#E2E8F0] bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Stock al cierre</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#0F172A]">{{ $stockCierre }}</p>
        </div>
    </div>

    {{-- ===================== TABLA DE MOVIMIENTOS ===================== --}}
    <h2 class="mt-8 text-lg font-semibold text-[#0F172A]">Movimientos del período</h2>

    @if ($filas->isEmpty())
        <div class="mt-4 rounded-lg border border-[#E2E8F0] bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
            No hubo movimientos en este período.
        </div>
    @else
        {{-- Mobile: tarjetas apiladas --}}
        <div class="mt-4 flex flex-col gap-3 sm:hidden">
            @foreach ($filas as $fila)
                @php $anulado = $fila['movimiento']->estaAnulado(); @endphp
                <div class="rounded-lg border border-[#E2E8F0] bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-medium {{ $anulado ? 'text-slate-400' : 'text-[#0F172A]' }}">{{ \Carbon\Carbon::parse($fila['movimiento']->fecha)->format('d/m/Y') }}</p>
                        <p class="flex items-center gap-2 text-sm font-medium {{ $anulado ? 'text-slate-400' : 'text-[#1E293B]' }}">
                            <span class="h-2 w-2 rounded-full {{ $anulado ? 'bg-slate-300' : ($fila['movimiento']->movimiento === 'entrada' ? 'bg-[#0D9488]' : 'bg-[#0F172A]') }}"></span>
                            {{ ucfirst($fila['movimiento']->movimiento) }}
                        </p>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-sm">
                        <p class="text-slate-500">
                            <span class="{{ $anulado ? 'text-slate-400 line-through' : '' }}">{{ $fila['movimiento']->cantidad }} {{ $fila['movimiento']->cantidad == 1 ? 'unidad' : 'unidades' }}</span>
                            · {{ $fila['movimiento']->empleado?->nombre ?? 'empleado eliminado' }}
                            @if ($anulado)
                                <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Anulado</span>
                            @endif
                        </p>
                        <p class="font-medium {{ $anulado ? 'text-slate-400' : 'text-[#0F172A]' }}">{{ $anulado ? '—' : 'Stock: ' . $fila['saldo'] }}</p>
                    </div>

                    @if ((Auth::user()->rol === 'admin' || $fila['movimiento']->id_empleado == Auth::id()) && !$anulado)
                        <form method="POST" action="/movimientos/{{ $fila['movimiento']->id }}/anular" class="mt-3 border-t border-[#E2E8F0] pt-3"
                              data-confirmar="¿Seguro de que querés anular este movimiento?" data-confirmar-boton="Anular">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-700 hover:underline">Anular</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Tablet/desktop: tabla --}}
        <div class="mt-4 hidden overflow-hidden rounded-lg border border-[#E2E8F0] bg-white shadow-sm sm:block">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-[#E2E8F0] bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Tipo</th>
                        <th class="px-6 py-3">Cantidad</th>
                        <th class="px-6 py-3">Empleado</th>
                        <th class="px-6 py-3 text-right">Stock resultante</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @foreach ($filas as $fila)
                        @php $anulado = $fila['movimiento']->estaAnulado(); @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 {{ $anulado ? 'text-slate-400' : 'text-slate-500' }}">{{ \Carbon\Carbon::parse($fila['movimiento']->fecha)->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">
                                <span class="flex items-center gap-2 font-medium {{ $anulado ? 'text-slate-400' : 'text-[#1E293B]' }}">
                                    <span class="h-2 w-2 rounded-full {{ $anulado ? 'bg-slate-300' : ($fila['movimiento']->movimiento === 'entrada' ? 'bg-[#0D9488]' : 'bg-[#0F172A]') }}"></span>
                                    {{ ucfirst($fila['movimiento']->movimiento) }}
                                    @if ($anulado)
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Anulado</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3 {{ $anulado ? 'text-slate-400 line-through' : 'text-slate-500' }}">{{ $fila['movimiento']->cantidad }}</td>
                            <td class="px-6 py-3 {{ $anulado ? 'text-slate-400' : 'text-slate-500' }}">{{ $fila['movimiento']->empleado?->nombre ?? 'empleado eliminado' }}</td>
                            <td class="px-6 py-3 text-right font-medium {{ $anulado ? 'text-slate-400' : 'text-[#0F172A]' }}">{{ $anulado ? '—' : $fila['saldo'] }}</td>
                            <td class="px-6 py-3 text-right">
                                @if ((Auth::user()->rol === 'admin' || $fila['movimiento']->id_empleado == Auth::id()) && !$anulado)
                                    <form method="POST" action="/movimientos/{{ $fila['movimiento']->id }}/anular"
                                          data-confirmar="¿Seguro de que querés anular este movimiento?" data-confirmar-boton="Anular">
                                        @csrf
                                        <button type="submit" class="font-medium text-slate-500 hover:text-slate-700 hover:underline">Anular</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
