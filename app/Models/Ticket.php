<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'codigo', 'solicitante_id', 'dependencia_id', 'local_id', 'canal_id', 'servicio_id', 'especialista_id',
        'servicio_directo', 'sla_id', 'dificultad_id',
        'prioridad_id', 'estado', 'asunto', 'celular', 'descripcion',
        'resolucion', 'motivo_cancelacion', 'fecha_inicio_atencion', 'fecha_resolucion',
        'es_padre', 'ticket_padre_id',
    ];

    public function prioridad()     { return $this->belongsTo(Prioridad::class); }
    public function especialista()  { return $this->belongsTo(Especialista::class); }
    public function dificultad()    { return $this->belongsTo(Dificultad::class); }
    public function solicitante()   { return $this->belongsTo(Trabajador::class, 'solicitante_id'); }
    public function dependencia()   { return $this->belongsTo(Dependencia::class); }
    public function local()         { return $this->belongsTo(Local::class); }
    public function canal()         { return $this->belongsTo(CanalRegistro::class); }
    public function servicio()      { return $this->belongsTo(Servicio::class); }
    public function solicitud()     { return $this->belongsTo(Solicitud::class); }
    public function historial()     { return $this->hasMany(TicketHistorial::class); }
    public function archivos()      { return $this->hasMany(TicketArchivo::class); }
    public function hijos()         { return $this->hasMany(Ticket::class, 'ticket_padre_id'); }
    public function padre()         { return $this->belongsTo(Ticket::class, 'ticket_padre_id'); }
}
