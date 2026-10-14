<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadors';

    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'area_id',
        'cargo_id',
        'codigo',
        'dni',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'sexo',
        'direccion',
        'telefono',
        'foto',
        'fecha_ingreso',
        'estado',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'estado' => 'boolean',
    ];


    /** Empresa a la que pertenece el trabajador  */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }


    /** Sucursal a la que pertenece el trabajador */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }


    /** Área a la que pertenece el trabajador */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }


    /** Cargo del trabajador */
    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }


    /** Horario actual del trabajador*/
    public function horarioActual()
    {
        return $this->hasOne(AsignacionHorario::class)
            ->where('estado', 1)
            ->latest('fecha_inicio');
    }

    /** Asistencias del trabajador */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }


    /** Usuario asociado al trabajador   */
    public function user()
    {
        return $this->hasOne(User::class);
    }


    /** Asignaciones de horario    */
    public function asignaciones()
    {
        return $this->hasMany(AsignacionHorario::class);
    }
}