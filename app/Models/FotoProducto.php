<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoProducto extends Model
{
    protected $table = 'fotos_producto';

    protected $fillable = ['id_producto', 'ruta'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}