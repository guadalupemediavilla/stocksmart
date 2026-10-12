<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['cantidad', 'id_variante', 'id_empleado', 'fecha', 'movimiento', 'anulado_at', 'anulado_por'];

    protected $casts = [
        'anulado_at' => 'datetime',
    ];

    public function variante()
    {
        return $this->belongsTo(Variante::class, 'id_variante');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function anuladoPor()
    {
        return $this->belongsTo(Empleado::class, 'anulado_por');
    }

    public function scopeVigentes($query)
    {
        return $query->whereNull('anulado_at');
    }

    public function estaAnulado(): bool
    {
        return !is_null($this->anulado_at);
    }

    public function dejariaStockNegativo(): bool
    {
        return $this->variante->quedariaNegativoConDelta($this->deltaAlAnular());
    }

    public function deltaAlAnular(): int
    {
        return $this->movimiento === 'entrada' ? -$this->cantidad : $this->cantidad;
    }
}
