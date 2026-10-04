<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $fillable = ['id_variante', 'id_empleado', 'precio', 'fecha'];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}