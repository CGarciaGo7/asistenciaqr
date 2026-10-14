<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    public function trabajador()
{
    return $this->belongsTo(Trabajador::class);
}
}
