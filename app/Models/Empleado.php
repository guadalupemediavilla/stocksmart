<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Empleado extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = ['nombre', 'apellido', 'mail', 'password', 'rol'];

    protected $hidden = ['password'];

    public function precios()
    {
        return $this->hasMany(Precio::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}