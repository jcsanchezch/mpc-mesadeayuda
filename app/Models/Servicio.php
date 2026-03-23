<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'categoria_id', 'tipo_id', 'activo'];

    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function tipo()      { return $this->belongsTo(Tipo::class); }
    public function formatos()  { return $this->hasMany(Formato::class)->where('activo', true); }
}
