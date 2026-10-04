<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empleado;

class EmpleadoController extends Controller
{
    public function index(Request $request)
{
    $query = Empleado::query();

    if ($request->filled('buscar')) {
        $query->where(function ($q) use ($request) {
            $q->where('nombre', 'like', '%' . $request->buscar . '%')
              ->orWhere('apellido', 'like', '%' . $request->buscar . '%');
        });
    }

    if ($request->filled('rol')) {
        $query->where('rol', $request->rol);
    }

    $empleados = $query->get();

    return view('empleados.index', ['empleados' => $empleados]);
}
    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        Empleado::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'mail' => $request->mail,
            'password' => bcrypt($request->password),
            'rol' => $request->rol,
        ]);

        return redirect('/empleados');
    }

    public function edit($id)
{
    $empleado = Empleado::find($id);
    return view('empleados.edit', ['empleado' => $empleado]);
}

public function update(Request $request, $id)
{
    $empleado = Empleado::find($id);

    $empleado->nombre = $request->nombre;
    $empleado->apellido = $request->apellido;
    $empleado->mail = $request->mail;
    $empleado->rol = $request->rol;

    if ($request->filled('password')) {
        $empleado->password = bcrypt($request->password);
    }

    $empleado->save();

    return redirect('/empleados');
}

   public function destroy($id)
{
    Empleado::find($id)->delete();

    if ($id == Auth::id()) {
        Auth::logout();
        return redirect('/login');
    }

    return redirect('/empleados');
}

}