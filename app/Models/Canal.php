<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    protected $table = 'canales';

    protected $fillable = ['codigo', 'label', 'es_aplicacion', 'activo'];

    public function tickets() { return $this->hasMany(Ticket::class); }
}
