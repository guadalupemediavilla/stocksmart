<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);

        $movimiento = Stock::create([
            'id_variante' => $datos['id_variante'],
            'cantidad' => $datos['cantidad'],
            'movimiento' => $datos['movimiento'],
            'id_empleado' => Auth::id(),
            'fecha' => now(),
        ]);

        return response()->json($movimiento, 201);
    }
}