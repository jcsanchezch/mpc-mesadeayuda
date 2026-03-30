<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanalRegistro extends Model
{
    protected $table = 'canales_registro';

    protected $fillable = ['codigo', 'label', 'activo'];

    public function tickets() { return $this->hasMany(Ticket::class); }
}
