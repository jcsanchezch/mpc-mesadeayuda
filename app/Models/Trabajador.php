<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $primaryKey = 'id';

    protected $fillable = [
        'users_id',
        'dni',
        'nombres',
        'apellidos',
        'telefono',
        'dependencias_id',
        'cargos_id',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
