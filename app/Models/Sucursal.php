<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Empresa;

class Sucursal extends Model
{
  protected $fillable = [
    'empresa_id',
    'nombre',
    'direccion',
    'telefono',
    'correo',
    'tipo_horario',
    'estado'
];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
    
}
