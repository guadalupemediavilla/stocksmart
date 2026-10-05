<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientoApiController extends Controller
{
    public function index()
    {
        $movimientos = Stock::with('variante.producto')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($movimientos);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_variante' => ['required', 'integer', 'exists:variantes,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'movimiento' => ['required', 'in:salida'],
            'fecha' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $movimiento = Stock::create([
            'id_variante' => $datos['id_variante'],
            'cantidad' => $datos['cantidad'],
            'movimiento' => $datos['movimiento'],
            'id_empleado' => Auth::id(),
            'fecha' => $datos['fecha'] ?? now(),
        ]);

        return response()->json($movimiento, 201);
    }

    public function lote(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['nullable', 'date', 'before_or_equal:today'],
            'movimientos' => ['required', 'array', 'min:1'],
            'movimientos.*.id_variante' => ['required', 'integer', 'exists:variantes,id'],
            'movimientos.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $fecha = $datos['fecha'] ?? now();
        $idEmpleado = Auth::id();

        $creados = DB::transaction(function () use ($datos, $fecha, $idEmpleado) {
            return collect($datos['movimientos'])->map(function ($item) use ($fecha, $idEmpleado) {
                return Stock::create([
                    'id_variante' => $item['id_variante'],
                    'cantidad' => $item['cantidad'],
                    'movimiento' => 'salida',
                    'id_empleado' => $idEmpleado,
                    'fecha' => $fecha,
                ]);
            });
        });

        return response()->json($creados, 201);
    }
}
