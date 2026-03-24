<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prioridad extends Model
{
    protected $table = 'prioridades';
    protected $fillable = ['codigo', 'label', 'text_color', 'bg_color', 'activo'];
}
