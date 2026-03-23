<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
    protected $table = 'dependencias';

    protected $fillable = ['nombre', 'abreviatura', 'activo'];

    public function trabajadores()  { return $this->hasMany(Trabajador::class); }
    public function responsable()   { return $this->belongsTo(Trabajador::class, 'responsable_id'); }
}
