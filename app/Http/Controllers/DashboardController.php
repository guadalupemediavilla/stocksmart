<?php

namespace App\Http\Controllers;
use App\Services\DolarService;

use App\Models\Producto;
use App\Models\Variante;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $variantes = Variante::whereHas('producto')->with(['precios', 'stocks', 'producto'])->get();

        $valorInventario = 0;
        $stockBajo = [];
        $valorPorProducto = [];

        foreach ($variantes as $variante) {
            $precioActual = $variante->precios->sortByDesc('fecha')->first();
            $tieneStock = $variante->stocks->isNotEmpty();
            $stockActual = $variante->stockActual();

            if ($precioActual && $tieneStock) {
                $subtotal = $precioActual->precio * $stockActual;
                $valorInventario += $subtotal;

                $nombreProducto = $variante->producto->nombre;
                if (!isset($valorPorProducto[$nombreProducto])) {
                    $valorPorProducto[$nombreProducto] = 0;
                }
                $valorPorProducto[$nombreProducto] += $subtotal;

                if ($stockActual <= 5) {
                    $stockBajo[] = [
                        'variante' => $variante,
                        'cantidad' => $stockActual,
                    ];
                }
            }
        }

       arsort($valorPorProducto);

$topProductos = array_slice($valorPorProducto, 0, 5, true);

if (count($valorPorProducto) > 5) {
    $restoSuma = array_sum(array_slice($valorPorProducto, 5, null, true));
    $topProductos['Otros'] = $restoSuma;
}

        $totalProductos = Producto::count();
        $totalVariantes = Variante::whereHas('producto')->count();

        $ultimosMovimientos = Stock::with(['variante.producto', 'empleado'])
            ->orderByDesc('fecha')
            ->take(10)
            ->get();

        $dolar = DolarService::cotizacionOficial();

        // ===== Filtro de mes/año para las tarjetas, el gráfico de movimientos y "más movidos" =====
        $anio = (int) $request->input('anio', now()->year);
        $mes = (int) $request->input('mes', now()->month);

        $primerMovimiento = Stock::min('fecha');
        $anioMinimo = $primerMovimiento ? Carbon::parse($primerMovimiento)->year : now()->year;
        $anioMaximo = now()->year;

        $movimientosMes = Stock::whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->whereHas('variante.producto')
            ->with('variante.producto')
            ->get();

        $entradasMes = $movimientosMes->where('movimiento', 'entrada')->sum('cantidad');
        $salidasMes = $movimientosMes->where('movimiento', 'salida')->sum('cantidad');

        $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;
        $entradasPorDia = array_fill(1, $diasEnMes, 0);
        $salidasPorDia = array_fill(1, $diasEnMes, 0);

        foreach ($movimientosMes as $movimiento) {
            $dia = Carbon::parse($movimiento->fecha)->day;

            if ($movimiento->movimiento === 'entrada') {
                $entradasPorDia[$dia] += $movimiento->cantidad;
            } else {
                $salidasPorDia[$dia] += $movimiento->cantidad;
            }
        }

        $masMovidos = $movimientosMes
            ->where('movimiento', 'salida')
            ->groupBy(fn($s) => $s->variante->producto->id)
            ->map(fn($grupo) => [
                'producto' => $grupo->first()->variante->producto,
                'cantidad' => $grupo->sum('cantidad'),
            ])
            ->sortByDesc('cantidad')
            ->take(5)
            ->values();

        return view('dashboard', [
            'dolar' => $dolar,
            'valorInventario' => $valorInventario,
            'stockBajo' => $stockBajo,
            'totalProductos' => $totalProductos,
            'totalVariantes' => $totalVariantes,
            'ultimosMovimientos' => $ultimosMovimientos,
           'valorPorProducto' => $topProductos,
            'anio' => $anio,
            'mes' => $mes,
            'anioMinimo' => $anioMinimo,
            'anioMaximo' => $anioMaximo,
            'entradasMes' => $entradasMes,
            'salidasMes' => $salidasMes,
            'entradasPorDia' => $entradasPorDia,
            'salidasPorDia' => $salidasPorDia,
            'masMovidos' => $masMovidos,
        ]);
    }
}