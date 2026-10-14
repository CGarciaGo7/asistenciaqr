<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionTurno extends Model
{
    protected $table = 'asignacion_turnos';

    protected $fillable = [
        'trabajador_id',
        'sucursal_id',
        'cargo_id',
        'horario_id',
        'fecha',
        'numero_semana',
        'anio',
        'es_descanso',
        'incidencias',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'es_descanso' => 'boolean',
            'numero_semana' => 'integer',
            'anio' => 'integer',
        ];
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }
}
