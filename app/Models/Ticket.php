<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'codigo', 'solicitante_id', 'servicio_id', 'especialista_id',
        'estado', 'asunto', 'descripcion', 'es_padre', 'ticket_padre_id',
    ];

    public function solicitante()   { return $this->belongsTo(User::class, 'solicitante_id'); }
    public function servicio()      { return $this->belongsTo(Servicio::class); }
    public function historial()     { return $this->hasMany(TicketHistorial::class); }
    public function archivos()      { return $this->hasMany(TicketArchivo::class); }
    public function hijos()         { return $this->hasMany(Ticket::class, 'ticket_padre_id'); }
    public function padre()         { return $this->belongsTo(Ticket::class, 'ticket_padre_id'); }
}
