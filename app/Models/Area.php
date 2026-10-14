<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    
    protected $fillable=[

        'nombre',
        'descripcion',
        'estado'

    ];

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
}
