<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Variante;
use App\Models\Precio;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class VarianteController extends Controller
{
 public function store(Request $request, $id_producto)
{
    $request->validate([
        'fotos'   => 'nullable|array',
        'fotos.*' => 'image|max:5120',
    ]);

    $nombres = $request->input('atributo_nombre', []);
    $valores = $request->input('atributo_valor', []);

    $atributos = [];
    foreach ($nombres as $indice => $nombre) {
        if (trim($nombre) !== '' && isset($valores[$indice]) && trim($valores[$indice]) !== '') {
            $atributos[$nombre] = $valores[$indice];
        }
    }

    $variante = Variante::create([
        'id_producto' => $id_producto,
        'sku' => $request->sku,
        'atributos' => json_encode($atributos),
    ]);

    Precio::create([
        'id_variante' => $variante->id,
        'id_empleado' => Auth::id(),
        'precio' => $request->precio,
        'fecha' => now(),
    ]);

    Stock::create([
        'id_variante' => $variante->id,
        'id_empleado' => Auth::id(),
        'cantidad' => $request->cantidad,
        'movimiento' => 'entrada',
        'fecha' => now(),
    ]);

    if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('variantes', 'public');
            $variante->fotos()->create(['ruta' => $ruta]);
        }
    }

    return redirect('/productos/' . $id_producto);
}

public function edit($id)
{
    $variante = Variante::with(['producto', 'fotos'])->findOrFail($id);

    $plantilla = json_decode($variante->producto->plantilla_atributos, true) ?? [];
    $atributos = json_decode($variante->atributos, true) ?? [];

    return view('variantes.edit', compact('variante', 'plantilla', 'atributos'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'sku'            => 'required|string|max:255',
        'fotos'          => 'nullable|array',
        'fotos.*'        => 'image|max:5120',
        'eliminar_fotos' => 'nullable|array',
    ]);

    $variante = Variante::findOrFail($id);

    // 1. Características (misma lógica que al crear: las vacías no se guardan)
    $nombres = $request->input('atributo_nombre', []);
    $valores = $request->input('atributo_valor', []);

    $atributos = [];
    foreach ($nombres as $indice => $nombre) {
        if (trim($nombre) !== '' && isset($valores[$indice]) && trim($valores[$indice]) !== '') {
            $atributos[$nombre] = $valores[$indice];
        }
    }

    $variante->update([
        'sku' => $request->sku,
        'atributos' => json_encode($atributos),
    ]);

    // 2. Borrar las fotos marcadas (la fila Y el archivo)
    $fotosABorrar = $variante->fotos()
        ->whereIn('id', $request->input('eliminar_fotos', []))
        ->get();

    foreach ($fotosABorrar as $foto) {
        Storage::disk('public')->delete($foto->ruta);
        $foto->delete();
    }

    // 3. Agregar las fotos nuevas
    if ($request->hasFile('fotos')) {
        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('variantes', 'public');
            $variante->fotos()->create(['ruta' => $ruta]);
        }
    }

    return redirect('/productos/' . $variante->id_producto);
}

    public function destroy($id)
    {
        $variante = Variante::find($id);
        $id_producto = $variante->id_producto;
        $variante->delete();

        return redirect('/productos/' . $id_producto);
    }

    public function movimientos(Request $request, $id)
    {
        $variante = Variante::with('producto')->findOrFail($id);

        $anio = (int) $request->input('anio', now()->year);
        $mes = (int) $request->input('mes', now()->month);
        $dia = $request->filled('dia') ? (int) $request->input('dia') : null;

        $primerMovimiento = $variante->stocks()->min('fecha');
        $anioMinimo = $primerMovimiento ? \Carbon\Carbon::parse($primerMovimiento)->year : now()->year;
        $anioMaximo = now()->year;

        $inicioPeriodo = \Carbon\Carbon::create($anio, $mes, $dia ?? 1)->startOfDay();
        $finPeriodo = $dia ? $inicioPeriodo->copy()->endOfDay() : $inicioPeriodo->copy()->endOfMonth()->endOfDay();

        $diasEnMes = \Carbon\Carbon::create($anio, $mes, 1)->daysInMonth;

        $stockInicio = $variante->stocks()
            ->where('fecha', '<', $inicioPeriodo->toDateString())
            ->get()
            ->groupBy('movimiento')
            ->map(fn($grupo) => $grupo->sum('cantidad'));

        $stockInicio = ($stockInicio['entrada'] ?? 0) - ($stockInicio['salida'] ?? 0);

        $movimientosPeriodo = $variante->stocks()
            ->with('empleado')
            ->whereBetween('fecha', [$inicioPeriodo->toDateString(), $finPeriodo->toDateString()])
            ->orderBy('fecha')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $entradasPeriodo = $movimientosPeriodo->where('movimiento', 'entrada')->sum('cantidad');
        $salidasPeriodo = $movimientosPeriodo->where('movimiento', 'salida')->sum('cantidad');
        $stockCierre = $stockInicio + $entradasPeriodo - $salidasPeriodo;

        $saldo = $stockInicio;
        $filas = $movimientosPeriodo->map(function ($movimiento) use (&$saldo) {
            $saldo += $movimiento->movimiento === 'entrada' ? $movimiento->cantidad : -$movimiento->cantidad;
            return [
                'movimiento' => $movimiento,
                'saldo' => $saldo,
            ];
        });

        return view('variantes.movimientos', [
            'variante' => $variante,
            'anio' => $anio,
            'mes' => $mes,
            'dia' => $dia,
            'anioMinimo' => $anioMinimo,
            'anioMaximo' => $anioMaximo,
            'diasEnMes' => $diasEnMes,
            'stockInicio' => $stockInicio,
            'entradasPeriodo' => $entradasPeriodo,
            'salidasPeriodo' => $salidasPeriodo,
            'stockCierre' => $stockCierre,
            'filas' => $filas,
        ]);
    }

    public function actualizar(Request $request, $id_variante)
    {
        if ($request->filled('precio')) {
            Precio::create([
                'id_variante' => $id_variante,
                'id_empleado' => Auth::id(),
                'precio' => $request->precio,
                'fecha' => now(),
            ]);
        }

        if ($request->filled('cantidad')) {
            Stock::create([
                'id_variante' => $id_variante,
                'id_empleado' => Auth::id(),
                'cantidad' => $request->cantidad,
                'movimiento' => $request->movimiento,
                'fecha' => now(),
            ]);
        }

        return redirect('/productos/' . $request->id_producto);
    }
}