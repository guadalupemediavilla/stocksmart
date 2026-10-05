<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Precio;

class PrecioApiController extends Controller
{
    public function index()
    {
        $precios = Precio::select('id', 'id_variante', 'precio', 'fecha')
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();

        return response()->json($precios);
    }
}
