<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{

protected $fillable = [
    'ruc',
    'razon_social',
    'nombre_comercial',
    'direccion',
    'telefono',
    'correo',
    'logo',
    'estado'
];

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }
    
}
