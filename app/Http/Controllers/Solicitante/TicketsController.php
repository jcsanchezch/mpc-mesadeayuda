<?php

namespace App\Http\Controllers\Solicitante;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Estado;
use App\Models\Servicio;
use App\Models\Ticket;
use App\Models\TicketHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketsController extends Controller
{
    public function index()
    {
        $todos = Ticket::with([
            'servicio.categoria',
            'historial.estadoNuevo',
            'historial.estadoAnterior',
            'historial.user',
            'archivos.archivo',
            'archivos.user',
        ])
        ->where('solicitante_id', Auth::id())
        ->latest()
        ->get()
        ->map(fn($t) => [
            'id'        => $t->id,
            'codigo'    => $t->codigo,
            'categoria' => $t->servicio?->categoria?->nombre,
            'servicio'  => $t->servicio?->nombre,
            'asunto'    => $t->asunto,
            'estado'    => $t->estado,
            'cerrado'      => in_array($t->estado, ['CANCELADO', 'CERRADO']),
            'fecha'        => $t->created_at?->format('d/m/Y H:i'),
            'descripcion'  => $t->descripcion,
            'resolucion'   => $t->resolucion,
            'archivos'     => $t->archivos->map(fn($a) => [
                'id'                  => $a->id,
                'nombre'              => $a->archivo?->filename_original,
                'peso'                => $a->archivo?->filesize_human,
                'mime'                => $a->archivo?->mime_type,
                'ruta'                => $a->archivo?->ruta,
                'tipo'                => $a->tipo,
                'firmado'             => $a->firmado_digitalmente,
                'usuario'             => trim("{$a->user?->nombres} {$a->user?->paterno}"),
            ]),
            'historial' => $t->historial->sortBy('created_at')->values()->map(fn($h) => [
                'id'              => $h->id,
                'estado_anterior' => $h->estadoAnterior?->nombre,
                'estado_nuevo'    => $h->estadoNuevo?->nombre,
                'usuario'         => trim("{$h->user?->nombres} {$h->user?->paterno}"),
                'comentario'      => $h->comentario,
                'es_conformidad'  => $h->es_conformidad,
                'fecha'           => $h->created_at?->format('d/m/Y H:i'),
            ]),
        ]);

        $activos  = $todos->where('cerrado', false)->values();
        $cerrados = $todos->where('cerrado', true)->values();

        $categorias = Categoria::with(['servicios' => function ($q) {
            $q->whereHas('tipo', fn($t) => $t->where('disponible_solicitante', true))
              ->where('activo', true)
              ->with(['formatos.archivo'])
              ->orderBy('nombre');
        }])
        ->whereHas('servicios', function ($q) {
            $q->whereHas('tipo', fn($t) => $t->where('disponible_solicitante', true))
              ->where('activo', true);
        })
        ->where('activo', true)
        ->orderBy('nombre')
        ->get(['id', 'nombre']);

        return Inertia::render('Solicitante/Tickets/Index', [
            'activos'    => $activos,
            'cerrados'   => $cerrados,
            'categorias' => $categorias,
        ]);
    }

    public function crearTicket(Request $request)
    {
        $validated = $request->validate([
            'modo'        => ['required', 'in:1,2'],
            'servicio_id' => ['nullable', 'integer', 'exists:servicios,id'],
            'asunto'      => ['required', 'string', 'max:500'],
            'descripcion' => ['nullable', 'string'],
        ]);

        if ($validated['modo'] === '2') {
            $request->validate(['servicio_id' => ['required']]);
            $validated['asunto'] = Servicio::findOrFail($validated['servicio_id'])->nombre;
        }

        $year  = now()->year;
        $last  = Ticket::whereYear('created_at', $year)->orderByDesc('id')->value('codigo');
        $seq   = 1;
        if ($last && preg_match('/TKT-\d{4}-(\d+)/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }
        $codigo = sprintf('TKT-%d-%05d', $year, $seq);

        $estadoInicio = Estado::where('es_inicio', true)->firstOrFail();

        $ticket = Ticket::create([
            'codigo'         => $codigo,
            'solicitante_id' => Auth::id(),
            'servicio_id'    => $validated['servicio_id'] ?? null,
            'estado'         => $estadoInicio->nombre,
            'asunto'         => $validated['asunto'],
            'descripcion'    => $validated['descripcion'] ?? null,
        ]);

        TicketHistorial::create([
            'ticket_id'          => $ticket->id,
            'estado_anterior_id' => null,
            'estado_nuevo_id'    => $estadoInicio->id,
            'user_id'            => Auth::id(),
            'comentario'         => 'Ticket creado.',
            'es_conformidad'     => false,
            'created_at'         => now(),
        ]);

        return redirect()->route('solicitante.index')
            ->with('success', "Ticket {$codigo} creado correctamente.");
    }

    public function conformidad(Request $request, Ticket $ticket)
    {
        abort_if($ticket->solicitante_id !== Auth::id(), 403);
        abort_if($ticket->estado !== 'ATENDIDO', 422, 'El ticket no está en estado ATENDIDO.');

        $estadoAtendido = Estado::where('nombre', 'ATENDIDO')->firstOrFail();
        $estadoCerrado  = Estado::where('nombre', 'CERRADO')->firstOrFail();

        TicketHistorial::create([
            'ticket_id'          => $ticket->id,
            'estado_anterior_id' => $estadoAtendido->id,
            'estado_nuevo_id'    => $estadoCerrado->id,
            'user_id'            => Auth::id(),
            'comentario'         => $request->input('comentario', 'Conformidad registrada por el solicitante.'),
            'es_conformidad'     => true,
            'created_at'         => now(),
        ]);

        $ticket->update(['estado' => 'CERRADO']);

        return redirect()->route('solicitante.index')
            ->with('success', 'Conformidad registrada. El ticket ha sido cerrado.');
    }
}
