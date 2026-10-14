<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    
    protected $fillable = [
            'nombre',
            'descripcion',
            'estado'
        ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
}
