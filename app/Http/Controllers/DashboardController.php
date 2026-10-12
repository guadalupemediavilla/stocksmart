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
            $tieneStock = $variante->stocks->reject->estaAnulado()->isNotEmpty();
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

        $dolar = DolarService::cotizacionOficial();

        // ===== Filtro de período (mes/año y día opcional) para el bloque inferior =====
        $anio = (int) $request->input('anio', now()->year);
        $mes = (int) $request->input('mes', now()->month);
        $dia = $request->filled('dia') ? (int) $request->input('dia') : null;

        $primerMovimiento = Stock::min('fecha');
        $anioMinimo = $primerMovimiento ? Carbon::parse($primerMovimiento)->year : now()->year;
        $anioMaximo = now()->year;

        $diasEnMes = Carbon::create($anio, $mes, 1)->daysInMonth;

        $inicioPeriodo = Carbon::create($anio, $mes, $dia ?? 1)->startOfDay();
        $finPeriodo = $dia ? $inicioPeriodo->copy()->endOfDay() : $inicioPeriodo->copy()->endOfMonth()->endOfDay();

        $movimientosPeriodo = Stock::vigentes()
            ->whereBetween('fecha', [$inicioPeriodo->toDateString(), $finPeriodo->toDateString()])
            ->whereHas('variante.producto')
            ->with('variante.producto')
            ->get();

        $entradasPeriodo = $movimientosPeriodo->where('movimiento', 'entrada')->sum('cantidad');
        $salidasPeriodo = $movimientosPeriodo->where('movimiento', 'salida')->sum('cantidad');

        $categoriasMovimientos = [];
        $serieEntradasMovimientos = [];
        $serieSalidasMovimientos = [];
        $valoresMovimientos = [];

        if ($dia) {
            $categoriasMovimientos = ['Entradas', 'Salidas'];
            $valoresMovimientos = [$entradasPeriodo, $salidasPeriodo];
        } else {
            $entradasPorDia = array_fill(1, $diasEnMes, 0);
            $salidasPorDia = array_fill(1, $diasEnMes, 0);

            foreach ($movimientosPeriodo as $movimiento) {
                $diaMovimiento = Carbon::parse($movimiento->fecha)->day;

                if ($movimiento->movimiento === 'entrada') {
                    $entradasPorDia[$diaMovimiento] += $movimiento->cantidad;
                } else {
                    $salidasPorDia[$diaMovimiento] += $movimiento->cantidad;
                }
            }

            $categoriasMovimientos = array_keys($entradasPorDia);
            $serieEntradasMovimientos = array_values($entradasPorDia);
            $serieSalidasMovimientos = array_values($salidasPorDia);
        }

        $ultimosMovimientos = Stock::vigentes()
            ->with(['variante.producto', 'empleado'])
            ->whereBetween('fecha', [$inicioPeriodo->toDateString(), $finPeriodo->toDateString()])
            ->orderByDesc('fecha')
            ->take(10)
            ->get();

        $masMovidos = $movimientosPeriodo
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
            'valorPorProducto' => $topProductos,
            'anio' => $anio,
            'mes' => $mes,
            'dia' => $dia,
            'anioMinimo' => $anioMinimo,
            'anioMaximo' => $anioMaximo,
            'diasEnMes' => $diasEnMes,
            'entradasPeriodo' => $entradasPeriodo,
            'salidasPeriodo' => $salidasPeriodo,
            'categoriasMovimientos' => $categoriasMovimientos,
            'serieEntradasMovimientos' => $serieEntradasMovimientos,
            'serieSalidasMovimientos' => $serieSalidasMovimientos,
            'valoresMovimientos' => $valoresMovimientos,
            'ultimosMovimientos' => $ultimosMovimientos,
            'masMovidos' => $masMovidos,
        ]);
    }
}