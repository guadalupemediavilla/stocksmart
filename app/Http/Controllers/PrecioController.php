<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Precio;

class PrecioController extends Controller
{
    public function store(Request $request, $id_variante)
    {
        $precio = Precio::create([
            'id_variante' => $id_variante,
            'id_empleado' => $request->id_empleado,
            'precio' => $request->precio,
            'fecha' => now(),
        ]);

        return redirect('/productos/' . $request->id_producto);
    }
}