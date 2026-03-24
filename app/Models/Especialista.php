<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialista extends Model
{
    protected $table = 'especialistas';
    protected $fillable = ['trabajador_id', 'vinculo_laboral', 'voluntario', 'activo'];

    public function trabajador() { return $this->belongsTo(Trabajador::class); }
}
