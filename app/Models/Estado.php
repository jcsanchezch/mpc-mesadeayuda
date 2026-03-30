<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';
    protected $fillable = ['codigo', 'label', 'label_solicitante', 'text_color', 'bg_color', 'es_inicio', 'es_fin', 'actor', 'activo'];
}
