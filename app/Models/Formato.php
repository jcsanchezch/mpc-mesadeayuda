<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formato extends Model
{
    protected $fillable = ['servicio_id', 'archivo_id', 'nombre', 'descripcion', 'activo'];

    public function servicio() { return $this->belongsTo(Servicio::class); }
    public function archivo()  { return $this->belongsTo(Archivo::class); }
}
