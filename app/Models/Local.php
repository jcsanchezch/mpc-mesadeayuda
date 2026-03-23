<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    protected $table = 'locales';

    protected $fillable = ['nombre', 'direccion', 'principal', 'activo'];

    public function trabajadores() { return $this->hasMany(Trabajador::class); }
}
