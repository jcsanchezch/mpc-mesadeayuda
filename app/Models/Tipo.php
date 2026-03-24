<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $fillable = ['codigo', 'label', 'descripcion', 'disponible_al_solicitante', 'activo'];

    public function servicios() { return $this->hasMany(Servicio::class); }
}
