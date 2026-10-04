<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['cantidad', 'id_variante', 'id_empleado', 'fecha', 'movimiento'];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}