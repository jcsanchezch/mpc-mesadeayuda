<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = ['origen_id', 'nombre', 'activo'];

    public function trabajadores() { return $this->hasMany(Trabajador::class); }
}
