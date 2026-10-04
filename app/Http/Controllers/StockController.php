<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

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
}