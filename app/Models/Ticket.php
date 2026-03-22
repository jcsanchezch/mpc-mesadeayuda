<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $table      = 'tickets';
    protected $primaryKey = 'id_ticket';

    protected $fillable = [
        'codigo',
        'id_servicio',
        'id_solicitante',
        'id_agente_mesa',
        'id_tecnico_asignado',
        'id_sla',
        'id_activo',
        'estado',
        'titulo',
        'descripcion',
        'canal_ingreso',
        'clasificacion',
        'prioridad',
        'urgencia',
        'impacto',
    ];

    protected function casts(): array
    {
        return [
            'conformidad_automatica' => 'boolean',
            'reabierto'              => 'boolean',
        ];
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'id_solicitante', 'id_trabajador');
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(TicketAdjunto::class, 'id_ticket', 'id_ticket');
    }
}
