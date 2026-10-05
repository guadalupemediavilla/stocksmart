<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index(Request $request)
{
    $query = Categoria::query();

    if ($request->filled('buscar')) {
        $query->where('nombre', 'like', '%' . $request->buscar . '%');
    }

    $categorias = $query->get();

    return view('categorias.index', ['categorias' => $categorias]);
}

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        Categoria::create([
            'nombre' => $request->nombre,
        ]);

        return redirect('/categorias');
    }

    public function storeAjax(Request $request)
{
    $categoria = Categoria::create([
        'nombre' => $request->nombre,
    ]);

    return response()->json($categoria);
}

    public function edit($id)
    {
        $categoria = Categoria::find($id);
        return view('categorias.edit', ['categoria' => $categoria]);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        $categoria->nombre = $request->nombre;
        $categoria->save();

        return redirect('/categorias');
    }

public function destroy($id)
{
    $categoria = Categoria::find($id);

    if ($categoria->productos()->count() > 0) {
        return redirect('/categorias')->with('error', 'No se puede borrar "' . $categoria->nombre . '" porque tiene productos activos asignados. Quitale la categoría a esos productos primero.');
    }

    $categoria->productos()->detach();
    $categoria->delete();

    return redirect('/categorias');
}
}