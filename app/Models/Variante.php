<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variante extends Model
{
    use SoftDeletes;

    protected $fillable = ['id_producto', 'sku', 'atributos'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function precios()
    {
        return $this->hasMany(Precio::class, 'id_variante');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'id_variante');
    }

    public function fotos()
    {
        return $this->hasMany(FotoVariante::class, 'id_variante');
    }

    public function stockActual()
    {
        $entradas = $this->stocks->where('movimiento', 'entrada')->sum('cantidad');
        $salidas  = $this->stocks->where('movimiento', 'salida')->sum('cantidad');

        return $entradas - $salidas;
    }
}