<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dificultad extends Model
{
    protected $table = 'dificultades';

    protected $fillable = ['nivel', 'codigo', 'label', 'color', 'activo'];

    public function tickets() { return $this->hasMany(Ticket::class); }
}
