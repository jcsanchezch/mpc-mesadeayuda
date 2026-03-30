<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketArchivo extends Model
{
    public $timestamps = false;

    protected $table = 'tickets_archivos';

    protected $fillable = [
        'ticket_id', 'archivo_id', 'user_id', 'historial_id',
        'formato_id', 'tipo', 'firmado_digitalmente',
    ];

    public function ticket()    { return $this->belongsTo(Ticket::class); }
    public function archivo()   { return $this->belongsTo(Archivo::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function historial() { return $this->belongsTo(TicketHistorial::class); }
    public function formato()   { return $this->belongsTo(Formato::class); }
}
