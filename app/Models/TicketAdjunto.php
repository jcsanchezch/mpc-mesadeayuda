<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAdjunto extends Model
{
    protected $table      = 'ticket_adjuntos';
    protected $primaryKey = 'id_adjunto';
    public    $timestamps = false;

    protected $fillable = [
        'id_ticket',
        'id_movimiento',
        'id_solicitud_info',
        'id_trabajador',
        'nombre_archivo',
        'ruta_almacen',
        'tamano_bytes',
        'tipo_mime',
        'visibilidad',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'id_ticket', 'id_ticket');
    }
}
