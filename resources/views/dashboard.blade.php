@extends('layouts.app')

@section('title', 'Panel de control · StockSmart')

@section('content')
<a href="/home" class="text-sm font-medium text-[#0D9488] hover:underline">← Volver al inicio</a>

    {{-- Alinea entradas y salidas por día (0 si no hubo movimiento) en orden cronológico --}}
    @php
        $dias = [];
        for ($i = 30; $i >= 0; $i--) {
            $dia = now()->subDays($i)->format('d/m');
            if (isset($entradasPorDia[$dia]) || isset($salidasPorDia[$dia])) {
                $dias[] = $dia;
            }
        }
        $dias = array_values(array_unique(array_merge($dias, array_keys($entradasPorDia + $salidasPorDia))));
        $serieEntradas = array_map(fn ($d) => $entradasPorDia[$d] ?? 0, $dias);
        $serieSalidas = array_map(fn ($d) => $salidasPorDia[$d] ?? 0, $dias);
    @endphp

    <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">Panel de control</h1>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-3 lg:gap-6">
        <div class="col-span-2 rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm lg:col-span-1">
            <p class="text-sm font-medium text-slate-500">Valor total del inventario</p>
            <p class="mt-2 break-words text-3xl font-bold tracking-tight text-[#0F172A]">${{ number_format($valorInventario, 2) }}</p>
            @if ($dolar)
                <p class="mt-1 text-sm text-slate-500">USD ${{ number_format($valorInventario / $dolar['venta'], 2) }}</p>
            @endif
        </div>

        <div class="rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Productos</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">{{ $totalProductos }}</p>
        </div>

        <div class="rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Variantes</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-[#0F172A]">{{ $totalVariantes }}</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-5">

        <div class="flex min-w-0 flex-col gap-6 lg:col-span-3">
            <section class="rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0F172A]">Valor de inventario por producto</h2>

                @if (count($valorPorProducto))
                    <div class="relative mt-4 min-w-0" style="height: {{ max(180, count($valorPorProducto) * 44 + 56) }}px">
                        <canvas id="graficoValorProducto" role="img" aria-label="Gráfico de barras del valor de inventario por producto"></canvas>
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500">Todavía no hay productos con precio y stock cargados.</p>
                @endif
            </section>

            <section class="rounded-lg border border-[#E2E8F0] bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0F172A]">Movimientos de los últimos 30 días</h2>

                @if (count($dias))
                    <div class="relative mt-4 h-64 min-w-0 sm:h-72">
                        <canvas id="graficoMovimientos" role="img" aria-label="Gráfico de líneas de entradas y salidas de stock por día"></canvas>
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500">No hubo movimientos en los últimos 30 días.</p>
                @endif
            </section>
        </div>

        <div class="flex min-w-0 flex-col gap-6 lg:col-span-2">
            <section class="rounded-lg border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4">
                    <h2 class="text-lg font-semibold text-[#0F172A]">Stock bajo</h2>
                    <p class="text-sm text-slate-500">5 unidades o menos</p>
                </div>

                <ul class="divide-y divide-[#E2E8F0]">
                    @forelse ($stockBajo as $item)
                        <li class="flex items-center justify-between gap-4 px-6 py-3">
                            <div class="min-w-0">
                                <p class="break-words font-medium text-[#0F172A]">{{ $item['variante']->producto->nombre }}</p>
                                <p class="text-sm text-slate-500">SKU: {{ $item['variante']->sku }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-[#D97706]/10 px-2.5 py-0.5 text-sm font-medium text-[#D97706]">
                                {{ $item['cantidad'] }} {{ $item['cantidad'] == 1 ? 'unidad' : 'unidades' }}
                            </span>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-sm text-slate-500">No hay variantes con stock bajo.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-lg border border-[#E2E8F0] bg-white shadow-sm">
                <div class="border-b border-[#E2E8F0] px-6 py-4">
                    <h2 class="text-lg font-semibold text-[#0F172A]">Últimos movimientos</h2>
                </div>

                <ul class="divide-y divide-[#E2E8F0]">
                    @forelse ($ultimosMovimientos as $movimiento)
                        <li class="flex items-start justify-between gap-4 px-6 py-3">
                            <div class="min-w-0">
                                <p class="break-words font-medium text-[#0F172A]">
                                    {{ $movimiento->variante?->producto?->nombre ?? 'Producto eliminado' }}
                                    @if ($movimiento->variante?->sku)
                                        <span class="font-normal text-slate-500">({{ $movimiento->variante->sku }})</span>
                                    @endif
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ $movimiento->fecha }} · por {{ $movimiento->empleado?->nombre ?? 'empleado eliminado' }}
                                </p>
                            </div>

                            <div class="shrink-0 text-right text-sm">
                                <p class="flex items-center justify-end gap-2 font-medium text-[#1E293B]">
                                    <span class="h-2 w-2 rounded-full {{ $movimiento->movimiento === 'entrada' ? 'bg-[#0D9488]' : 'bg-[#0F172A]' }}"></span>
                                    {{ ucfirst($movimiento->movimiento) }}
                                </p>
                                <p class="text-slate-500">{{ $movimiento->cantidad }} {{ $movimiento->cantidad == 1 ? 'unidad' : 'unidades' }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-sm text-slate-500">Todavía no hay movimientos registrados.</li>
                    @endforelse
                </ul>
            </section>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#64748B';
        Chart.defaults.borderColor = '#E2E8F0';

        const tooltipEstilo = {
            backgroundColor: '#0F172A',
            titleColor: '#FFFFFF',
            bodyColor: '#FFFFFF',
            padding: 10,
            cornerRadius: 6
        };

        const ctxValorProducto = document.getElementById('graficoValorProducto');

        if (ctxValorProducto) {
            const nombresProductos = {!! json_encode(array_keys($valorPorProducto)) !!};

            new Chart(ctxValorProducto, {
                type: 'bar',
                data: {
                    labels: nombresProductos,
                    datasets: [{
                        label: 'Valor de inventario ($)',
                        data: {!! json_encode(array_values($valorPorProducto)) !!},
                        backgroundColor: nombresProductos.map(n => n === 'Otros' ? '#94A3B8' : '#0D9488'),
                        borderRadius: 4,
                        maxBarThickness: 28
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipEstilo,
                            displayColors: false,
                            callbacks: {
                                label: c => '$' + c.parsed.x.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            border: { display: false },
                            ticks: {
                                maxRotation: 0,
                                maxTicksLimit: 6,
                                callback: v => '$' + v.toLocaleString('es-AR', { notation: 'compact', maximumFractionDigits: 1 })
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                callback(v) {
                                    const nombre = this.getLabelForValue(v);
                                    return nombre.length > 20 ? nombre.slice(0, 19) + '…' : nombre;
                                }
                            }
                        }
                    }
                }
            });
        }

        const ctxMovimientos = document.getElementById('graficoMovimientos');

        if (ctxMovimientos) {
            new Chart(ctxMovimientos, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dias) !!},
                    datasets: [
                        {
                            label: 'Entradas',
                            data: {!! json_encode($serieEntradas) !!},
                            borderColor: '#0D9488',
                            backgroundColor: '#0D9488',
                            borderWidth: 2,
                            cubicInterpolationMode: 'monotone',
                            pointStyle: 'circle',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Salidas',
                            data: {!! json_encode($serieSalidas) !!},
                            borderColor: '#0F172A',
                            backgroundColor: '#0F172A',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            cubicInterpolationMode: 'monotone',
                            pointStyle: 'rectRot',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, boxHeight: 8, padding: 20, color: '#1E293B' }
                        },
                        tooltip: {
                            ...tooltipEstilo,
                            usePointStyle: true,
                            callbacks: {
                                label: c => c.dataset.label + ': ' + c.parsed.y + (c.parsed.y === 1 ? ' unidad' : ' unidades')
                            }
                        }
                    },
                    scales: {
                        x: { offset: true, grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        }
    </script>
@endsection
