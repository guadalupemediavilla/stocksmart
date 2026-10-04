<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;


class Producto extends Model
{
    
 use SoftDeletes;

protected $fillable = ['nombre', 'descripcion', 'plantilla_atributos'];


   public function categorias()
{
    return $this->belongsToMany(Categoria::class, 'categoria_producto', 'id_producto', 'id_categoria');
}
public function variantes()
{
    return $this->hasMany(Variante::class, 'id_producto');
}

public function fotos()
{
    return $this->hasMany(FotoProducto::class, 'id_producto');
}
}
