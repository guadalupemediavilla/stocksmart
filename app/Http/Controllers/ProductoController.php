<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Empleado;
use App\Services\DolarService;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
public function index(Request $request)
{
    $query = Producto::query()->with('categorias');

    if ($request->filled('buscar')) {
        $query->where('nombre', 'like', '%' . $request->buscar . '%');
    }

    if ($request->filled('categorias')) {
        $query->whereHas('categorias', function ($q) use ($request) {
            $q->whereIn('categorias.id', $request->categorias);
        });
    }

   if ($request->filled('atributo')) {
    $query->whereHas('variantes', function ($q) use ($request) {
        $q->whereRaw('LOWER(atributos) LIKE ?', ['%' . strtolower($request->atributo) . '%']);
    });
}

    $productos = $query->distinct()->get();
    $categorias = Categoria::all();

    return view('productos.index', [
        'productos' => $productos,
        'categorias' => $categorias,
    ]);
}
 
public function create()
{
  $categorias = Categoria::all();
 return view('productos.create', ['categorias' => $categorias]);
}

public function store(Request $request)
{
    $request->validate([
        'nombre'  => 'required|string|max:255',
        'fotos'   => 'nullable|array',
        'fotos.*' => 'image|max:5120',
    ]);

    $plantilla = array_values(array_filter(
        $request->input('plantilla_nombre', []),
        fn($nombre) => trim($nombre) !== ''
    ));

    $producto = Producto::create([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'plantilla_atributos' => $plantilla ? json_encode($plantilla) : null,
    ]);

    $producto->categorias()->attach($request->id_categoria);

    if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('productos', 'public');
            $producto->fotos()->create(['ruta' => $ruta]);
        }
    }

    return redirect('/productos/' . $producto->id);
}

public function edit($id)
{
    $producto = Producto::find($id);
    $categorias = Categoria::all();
    return view('productos.edit', ['producto' => $producto, 'categorias' => $categorias]);
}

public function show($id)
{
    $producto = Producto::find($id);
    $producto->load('variantes.precios', 'variantes.stocks', 'categorias');
    $empleados = Empleado::all();
    $dolar = DolarService::cotizacionOficial();
    $plantilla = $producto->plantilla_atributos ? json_decode($producto->plantilla_atributos, true) : [];
    return view('productos.show', [
        'producto' => $producto,
        'empleados' => $empleados,
        'dolar' => $dolar,
        'plantilla' => $plantilla,
    ]);
}

public function update(Request $request, $id)
{
    $request->validate([
        'nombre'         => 'required|string|max:255',
        'fotos'          => 'nullable|array',
        'fotos.*'        => 'image|max:5120',
        'eliminar_fotos' => 'nullable|array',
    ]);

    $producto = Producto::findOrFail($id);

    $plantilla = array_values(array_filter(
        $request->input('plantilla_nombre', []),
        fn($nombre) => trim($nombre) !== ''
    ));

    $producto->nombre = $request->nombre;
    $producto->descripcion = $request->descripcion;
    $producto->plantilla_atributos = $plantilla ? json_encode($plantilla) : null;
    $producto->save();

    $producto->categorias()->sync($request->input('categorias', []));

    // Borrar las fotos marcadas (la fila Y el archivo), solo entre las de este producto
    $fotosABorrar = $producto->fotos()
        ->whereIn('id', $request->input('eliminar_fotos', []))
        ->get();

    foreach ($fotosABorrar as $foto) {
        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();
    }

    // Agregar las fotos nuevas
    if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('productos', 'public');
            $producto->fotos()->create(['ruta' => $ruta]);
        }
    }

    return redirect('/productos/' . $producto->id);
}

public function destroy($id)
{
    $producto = Producto::find($id);
    $producto->delete();

    return redirect('/productos');
}


}