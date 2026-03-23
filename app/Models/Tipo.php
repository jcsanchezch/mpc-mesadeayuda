<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'disponible_solicitante', 'activo'];

    public function servicios() { return $this->hasMany(Servicio::class); }
}
