<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketAdjunto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(): Response
    {
        $trabajador = auth()->user()->trabajador;

        $tickets = Ticket::where('id_solicitante', $trabajador->id_trabajador)
            ->orderByDesc('created_at')
            ->get([
                'id_ticket',
                'codigo',
                'titulo',
                'estado',
                'clasificacion',
                'created_at',
            ]);

        return Inertia::render('MisTickets/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('MisTickets/Create');
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $trabajador = auth()->user()->trabajador;

        DB::transaction(function () use ($request, $trabajador) {
            $year   = now()->year;
            $count  = Ticket::whereYear('created_at', $year)->lockForUpdate()->count();
            $codigo = sprintf('TKT-%d-%05d', $year, $count + 1);

            $ticket = Ticket::create([
                'codigo'       => $codigo,
                'id_solicitante' => $trabajador->id_trabajador,
                'titulo'       => $request->titulo,
                'descripcion'  => $request->descripcion,
                'estado'       => 'nuevo',
                'canal_ingreso' => 'portal',
            ]);

            if ($request->hasFile('adjuntos')) {
                foreach ($request->file('adjuntos') as $file) {
                    $ruta = $file->store("adjuntos/tickets/{$ticket->id_ticket}", 'private');

                    TicketAdjunto::create([
                        'id_ticket'      => $ticket->id_ticket,
                        'id_trabajador'  => $trabajador->id_trabajador,
                        'nombre_archivo' => $file->getClientOriginalName(),
                        'ruta_almacen'   => $ruta,
                        'tamano_bytes'   => $file->getSize(),
                        'tipo_mime'      => $file->getMimeType(),
                        'visibilidad'    => 'todos',
                    ]);
                }
            }
        });

        return redirect()->route('mis-tickets.index')
            ->with('status', 'Ticket registrado correctamente.');
    }
}
