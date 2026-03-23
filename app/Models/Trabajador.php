<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $fillable = [
        'origen_id', 'dependencia_id', 'cargo_id', 'local_id',
        'dni', 'paterno', 'materno', 'nombres', 'celular', 'activo',
    ];

    public function user()        { return $this->hasOne(User::class); }
    public function local()       { return $this->belongsTo(Local::class); }
    public function dependencia() { return $this->belongsTo(Dependencia::class); }
    public function cargo()       { return $this->belongsTo(Cargo::class); }
}
