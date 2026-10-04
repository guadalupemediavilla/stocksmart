<?php

namespace App\Http\Controllers;
use App\Services\DolarService;

use App\Models\Producto;
use App\Models\Variante;
use App\Models\Stock;


class DashboardController extends Controller
{
    public function index()
    {
        $variantes = Variante::whereHas('producto')->with(['precios', 'stocks', 'producto'])->get();

        $valorInventario = 0;
        $stockBajo = [];
        $valorPorProducto = [];

        foreach ($variantes as $variante) {
            $precioActual = $variante->precios->sortByDesc('fecha')->first();
            $stockActual = $variante->stocks->sortByDesc('fecha')->first();

            if ($precioActual && $stockActual) {
                $subtotal = $precioActual->precio * $stockActual->cantidad;
                $valorInventario += $subtotal;

                $nombreProducto = $variante->producto->nombre;
                if (!isset($valorPorProducto[$nombreProducto])) {
                    $valorPorProducto[$nombreProducto] = 0;
                }
                $valorPorProducto[$nombreProducto] += $subtotal;

                if ($stockActual->cantidad <= 5) {
                    $stockBajo[] = [
                        'variante' => $variante,
                        'cantidad' => $stockActual->cantidad,
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

        $movimientos30dias = Stock::where('fecha', '>=', now()->subDays(30))->get();

        $entradasPorDia = [];
        $salidasPorDia = [];

        foreach ($movimientos30dias as $movimiento) {
            $dia = \Carbon\Carbon::parse($movimiento->fecha)->format('d/m');

            if ($movimiento->movimiento === 'entrada') {
                $entradasPorDia[$dia] = ($entradasPorDia[$dia] ?? 0) + $movimiento->cantidad;
            } else {
                $salidasPorDia[$dia] = ($salidasPorDia[$dia] ?? 0) + $movimiento->cantidad;
            }
        }
        $dolar = DolarService::cotizacionOficial();

        return view('dashboard', [
            'dolar' => $dolar,
            'valorInventario' => $valorInventario,
            'stockBajo' => $stockBajo,
            'totalProductos' => $totalProductos,
            'totalVariantes' => $totalVariantes,
            'ultimosMovimientos' => $ultimosMovimientos,
           'valorPorProducto' => $topProductos,
            'entradasPorDia' => $entradasPorDia,
            'salidasPorDia' => $salidasPorDia,
        ]);
    }
}