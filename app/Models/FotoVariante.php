<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoVariante extends Model
{
    protected $table = 'fotos_variante';

    protected $fillable = ['id_variante', 'ruta'];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }
}