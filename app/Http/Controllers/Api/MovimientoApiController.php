<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;

class MovimientoApiController extends Controller
{
    public function index()
    {
        $movimientos = Stock::with('variante.producto')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($movimientos);
    }
}