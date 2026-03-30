<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes';
    protected $fillable = ['nombre', 'descripcion', 'servicio_id', 'activo'];

    public function servicio() { return $this->belongsTo(Servicio::class); }
}
