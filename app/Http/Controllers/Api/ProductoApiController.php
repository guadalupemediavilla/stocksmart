<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoApiController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categorias', 'variantes')->get();

        return response()->json($productos);
    }

    public function show($id)
    {
        $producto = Producto::with('categorias', 'variantes')->find($id);

        if (!$producto) {
            return response()->json(['mensaje' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }

    public function store(Request $request)
{
    $producto = Producto::create([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
    ]);

    if ($request->has('categorias')) {
        $producto->categorias()->attach($request->categorias);
    }

    return response()->json($producto, 201);
}

public function update(Request $request, $id)
{
    $producto = Producto::find($id);

    if (!$producto) {
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }

    $producto->update($request->only(['nombre', 'descripcion']));

    return response()->json($producto);
}

public function destroy($id)
{
    $producto = Producto::find($id);

    if (!$producto) {
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }

    $producto->delete();

    return response()->json(['message' => 'Producto eliminado correctamente']);
}
}