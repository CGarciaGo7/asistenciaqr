<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionHorario extends Model
{
        protected $fillable = [
        'trabajador_id',
        'horario_id',
        'fecha_inicio',
        'fecha_fin',
        'observacion',
        'estado',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
    
}
