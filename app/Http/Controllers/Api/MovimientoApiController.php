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

    public function anular($id)
    {
        $movimiento = Stock::find($id);

        if (!$movimiento) {
            return response()->json(['mensaje' => 'Movimiento no encontrado'], 404);
        }

        if ($movimiento->estaAnulado()) {
            return response()->json(['mensaje' => 'El movimiento ya estaba anulado'], 422);
        }

        if ($movimiento->dejariaStockNegativo()) {
            return response()->json(['mensaje' => 'No se puede anular: el stock de la variante quedaría negativo.'], 422);
        }

        $movimiento->update([
            'anulado_at' => now(),
            'anulado_por' => Auth::id(),
        ]);

        return response()->json($movimiento);
    }

    public function anularLote(Request $request)
    {
        $datos = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer'],
        ]);

        $idEmpleado = Auth::id();

        return DB::transaction(function () use ($datos, $idEmpleado) {
            $movimientos = Stock::with('variante')
                ->whereIn('id', $datos['ids'])
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $faltantes = collect($datos['ids'])->unique()->diff($movimientos->keys());

            if ($faltantes->isNotEmpty()) {
                return response()->json(['mensaje' => 'Movimiento no encontrado: '.$faltantes->implode(', ')], 404);
            }

            $yaAnulado = $movimientos->first(fn ($movimiento) => $movimiento->estaAnulado());

            if ($yaAnulado) {
                return response()->json(['mensaje' => "El movimiento {$yaAnulado->id} ya estaba anulado"], 422);
            }

            $negativo = $movimientos->groupBy('id_variante')->contains(function ($grupo) {
                $deltaTotal = $grupo->sum(fn ($movimiento) => $movimiento->deltaAlAnular());

                return $grupo->first()->variante->quedariaNegativoConDelta($deltaTotal);
            });

            if ($negativo) {
                return response()->json(['mensaje' => 'No se puede anular: el stock de la variante quedaría negativo.'], 422);
            }

            foreach ($movimientos as $movimiento) {
                $movimiento->update([
                    'anulado_at' => now(),
                    'anulado_por' => $idEmpleado,
                ]);
            }

            return response()->json($movimientos->values());
        });
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

    /**
     * Crea varias entradas como una sola operación atómica (todas o ninguna), todas con
     * la misma fecha. Se usa para devolver stock al anular una venta de kit o un ajuste
     * registrado desde una app externa.
     */
    public function loteEntradas(Request $request)
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
                    'movimiento' => 'entrada',
                    'id_empleado' => $idEmpleado,
                    'fecha' => $fecha,
                ]);
            });
        });

        return response()->json($creados, 201);
    }
}
