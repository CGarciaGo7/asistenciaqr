<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{

 protected $fillable = [
        'empresa_id',
        'nombre',
        'tipo_turno',
        'hora_ingreso',
        'hora_salida',
        'hora_ingreso2',
        'hora_salida2',
        'refrigerio',
        'tolerancia',
        'observacion',
        'estado'
    ];


public function trabajadores()
{
    return $this->hasMany(Trabajador::class);
}

public function asignaciones()
{
    return $this->hasMany(AsignacionHorario::class);
}

public function empresa()
{
    return $this->belongsTo(Empresa::class);
}

    const CONTINUO = 'CONTINUO';

    const PARTIDO = 'PARTIDO';

}
