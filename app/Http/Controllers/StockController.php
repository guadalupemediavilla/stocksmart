<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function store(Request $request, $id_variante)
    {
        $stock = Stock::create([
            'id_variante' => $id_variante,
            'id_empleado' => $request->id_empleado,
            'cantidad' => $request->cantidad,
            'movimiento' => $request->movimiento,
            'fecha' => now(),
        ]);

        return redirect('/productos/' . $request->id_producto);
    }

    public function anular($id)
    {
        $stock = Stock::findOrFail($id);

        if (Auth::user()->rol !== 'admin' && $stock->id_empleado != Auth::id()) {
            return redirect()->back()->with('error', 'Solo podés anular movimientos que cargaste vos.');
        }

        if ($stock->estaAnulado()) {
            return redirect()->back();
        }

        if ($stock->dejariaStockNegativo()) {
            return redirect()->back()->with('error', 'No se puede anular: el stock de la variante quedaría negativo.');
        }

        $stock->update([
            'anulado_at' => now(),
            'anulado_por' => Auth::id(),
        ]);

        return redirect()->back();
    }
}