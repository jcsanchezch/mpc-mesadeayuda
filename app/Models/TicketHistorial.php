<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHistorial extends Model
{
    public $table = 'tickets_historial';
    public $timestamps = false;

    protected $fillable = [
        'ticket_id', 'estado_anterior_id', 'estado_nuevo_id',
        'user_id', 'comentario', 'es_conformidad', 'created_at',
    ];

    protected $attributes = ['created_at' => null];

    protected $casts = ['created_at' => 'datetime'];

    public function ticket()         { return $this->belongsTo(Ticket::class); }
    public function estadoNuevo()    { return $this->belongsTo(Estado::class, 'estado_nuevo_id'); }
    public function estadoAnterior() { return $this->belongsTo(Estado::class, 'estado_anterior_id'); }
    public function user()           { return $this->belongsTo(User::class); }
}
