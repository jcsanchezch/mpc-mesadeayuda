<?php

namespace App\Http\Controllers\Solicitante;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\CanalRegistro;
use App\Models\Categoria;
use App\Models\Dependencia;
use App\Models\Estado;
use App\Models\Local;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Ticket;
use App\Models\TicketArchivo;
use App\Models\TicketHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
                'ruta'                => $a->archivo?->ruta
                    ? Storage::disk('minio')->temporaryUrl($a->archivo->ruta, now()->addHour())
                    : null,
                'tipo'                => $a->tipo,
                'firmado'             => $a->firmado_digitalmente,
                'usuario'             => trim("{$a->user?->nombres} {$a->user?->paterno}"),
            ]),
            'historial' => $t->historial->sortBy('created_at')->values()->map(fn($h) => [
                'id'              => $h->id,
                'estado_anterior' => $h->estadoAnterior?->codigo,
                'estado_nuevo'    => $h->estadoNuevo?->codigo,
                'usuario'         => trim("{$h->user?->nombres} {$h->user?->paterno}"),
                'comentario'      => $h->comentario,
                'es_conformidad'  => $h->es_conformidad,
                'fecha'           => $h->created_at?->format('d/m/Y H:i'),
            ]),
        ]);

        $activos  = $todos->where('cerrado', false)->values();
        $cerrados = $todos->where('cerrado', true)->values();

        $categorias = Categoria::with(['servicios' => function ($q) {
            $q->whereHas('tipo', fn($t) => $t->where('disponible_al_solicitante', true))
              ->where('activo', true)
              ->with(['formatos.archivo'])
              ->orderBy('nombre');
        }])
        ->whereHas('servicios', function ($q) {
            $q->whereHas('tipo', fn($t) => $t->where('disponible_al_solicitante', true))
              ->where('activo', true);
        })
        ->where('activo', true)
        ->orderBy('nombre')
        ->get(['id', 'nombre']);

        $user       = Auth::user()->load('trabajador.dependencia', 'trabajador.local');
        $trabajador = $user->trabajador;

        $dependencias = Dependencia::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $locales      = Local::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Solicitante/Tickets/Index', [
            'activos'      => $activos,
            'cerrados'     => $cerrados,
            'categorias'   => $categorias,
            'dependencias' => $dependencias,
            'locales'      => $locales,
            'solicitante'  => [
                'nombre'         => trim("{$user->dni} - {$user->paterno} {$user->materno} {$user->nombres}"),
                'dependencia_id' => $trabajador?->dependencia_id,
                'dependencia'    => $trabajador?->dependencia?->nombre,
                'local_id'       => $trabajador?->local_id,
                'local'          => $trabajador?->local?->nombre,
                'celular'        => $trabajador?->celular,
            ],
        ]);
    }

    public function crearVista()
    {
        $categorias = Categoria::with(['servicios' => function ($q) {
            $q->where('activo', true)
              ->with([
                  'formatos.archivo',
                  'solicitudes' => fn($sq) => $sq->where('activo', true)->orderBy('nombre'),
              ])
              ->orderBy('nombre');
        }])
        ->whereHas('servicios', function ($q) {
            $q->where('activo', true)
              ->whereHas('solicitudes', fn($sq) => $sq->where('activo', true));
        })
        ->where('activo', true)
        ->orderBy('nombre')
        ->get(['id', 'nombre']);

        $user       = Auth::user()->load('trabajador.dependencia', 'trabajador.local');
        $trabajador = $user->trabajador;

        $dependencias = Dependencia::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $locales      = Local::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Solicitante/Tickets/Crear', [
            'categorias'   => $categorias,
            'dependencias' => $dependencias,
            'locales'      => $locales,
            'solicitante'  => [
                'nombre'         => trim("{$user->dni} - {$user->paterno} {$user->materno} {$user->nombres}"),
                'dependencia_id' => $trabajador?->dependencia_id,
                'local_id'       => $trabajador?->local_id,
                'celular'        => $trabajador?->celular,
            ],
        ]);
    }

    public function crearTicket(Request $request)
    {
        $validated = $request->validate([
            'modo'           => ['required', 'in:1,2'],
            'solicitud_id'   => ['nullable', 'integer', 'exists:solicitudes,id'],
            'dependencia_id' => ['nullable', 'integer', 'exists:dependencias,id'],
            'local_id'       => ['nullable', 'integer', 'exists:locales,id'],
            'asunto'         => ['required_if:modo,1', 'nullable', 'string', 'max:500'],
            'celular'        => ['nullable', 'string', 'max:15'],
            'descripcion'    => ['nullable', 'string'],
            'archivos'       => ['nullable', 'array', 'max:5'],
            'archivos.*'     => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif'],
        ], [], ['solicitud_id' => 'solicitud']);

        $solicitud = null;
        if ($validated['modo'] === '2') {
            $request->validate(
                ['solicitud_id' => ['required', 'integer', 'exists:solicitudes,id']],
                [],
                ['solicitud_id' => 'solicitud']
            );
            $solicitud = Solicitud::with('servicio')->findOrFail($validated['solicitud_id']);
            $validated['asunto'] = $solicitud->nombre;
        }

        $year  = now()->year;
        $last  = Ticket::whereYear('created_at', $year)->orderByDesc('id')->value('codigo');
        $seq   = 1;
        if ($last && preg_match('/\d{4}-(\d+)/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }
        $codigo = sprintf('%d-%05d', $year, $seq);

        $estadoInicio = Estado::where('es_inicio', true)->firstOrFail();
        $canalMesa    = CanalRegistro::where('codigo', 'APLICACION')->firstOrFail();

        $trabajador = Auth::user()->trabajador;

        $ticket = Ticket::create([
            'codigo'           => $codigo,
            'solicitante_id'   => $trabajador?->id ?? Auth::id(),
            'dependencia_id'   => $validated['dependencia_id'] ?? $trabajador?->dependencia_id,
            'local_id'         => $validated['local_id'] ?? $trabajador?->local_id,
            'canal_id'         => $canalMesa->id,
            'solicitud_id'     => $solicitud?->id,
            'servicio_id'      => $solicitud?->servicio_id,
            'servicio_directo' => $validated['modo'] === '2',
            'estado'           => $estadoInicio->codigo,
            'asunto'           => $validated['asunto'],
            'celular'          => $validated['celular'] ?? null,
            'descripcion'      => $validated['descripcion'] ?? null,
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

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $carpeta  = "tickets/{$codigo}";
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $ruta     = "{$carpeta}/{$filename}";

                Storage::disk('minio')->putFileAs($carpeta, $file, $filename, 'private');

                $bytes   = $file->getSize();
                $archivo = Archivo::create([
                    'filename'          => $filename,
                    'filename_original' => $file->getClientOriginalName(),
                    'filesize'          => $bytes,
                    'filesize_human'    => $this->humanSize($bytes),
                    'hash'              => hash_file('sha256', $file->getRealPath()),
                    'mime_type'         => $file->getMimeType(),
                    'carpeta'           => $carpeta,
                    'ruta'              => $ruta,
                ]);

                TicketArchivo::create([
                    'ticket_id'            => $ticket->id,
                    'archivo_id'           => $archivo->id,
                    'user_id'              => Auth::id(),
                    'tipo'                 => 'adjunto',
                    'firmado_digitalmente' => false,
                ]);
            }
        }

        return redirect()->route('solicitante.index')
            ->with('success', "Ticket {$codigo} creado correctamente.");
    }

    private function humanSize(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) return "{$bytes} {$unit}";
            $bytes = (int) round($bytes / 1024);
        }
        return "{$bytes} TB";
    }

    public function verVista(Ticket $ticket)
    {
        abort_if($ticket->solicitante_id !== Auth::id(), 403);

        $ticket->load([
            'servicio.categoria',
            'dependencia',
            'local',
            'prioridad',
            'solicitante',
            'historial.estadoNuevo',
            'historial.estadoAnterior',
            'historial.user',
            'archivos.archivo',
            'archivos.user',
        ]);

        $estados     = Estado::where('activo', true)->get(['codigo', 'label', 'text_color', 'bg_color']);
        $prioridades = \App\Models\Prioridad::where('activo', true)->get(['codigo', 'label', 'text_color', 'bg_color']);

        return Inertia::render('Solicitante/Tickets/Ver', [
            'estados'     => $estados,
            'prioridades' => $prioridades,
            'ticket'      => [
                'id'          => $ticket->id,
                'codigo'      => $ticket->codigo,
                'dni'         => $ticket->solicitante?->dni,
                'solicitante' => trim("{$ticket->solicitante?->paterno} {$ticket->solicitante?->materno} {$ticket->solicitante?->nombres}"),
                'categoria'   => $ticket->servicio?->categoria?->nombre,
                'servicio'    => $ticket->servicio?->nombre,
                'asunto'      => $ticket->asunto,
                'estado'      => $ticket->estado,
                'prioridad'   => $ticket->prioridad?->codigo,
                'cerrado'     => in_array($ticket->estado, ['CANCELADO', 'CERRADO']),
                'fecha'       => $ticket->created_at?->format('d/m/Y H:i'),
                'descripcion' => $ticket->descripcion,
                'resolucion'  => $ticket->resolucion,
                'dependencia' => $ticket->dependencia?->nombre,
                'local'       => $ticket->local?->nombre,
                'celular'     => $ticket->celular,
                'archivos'    => $ticket->archivos->map(fn($a) => [
                    'id'      => $a->id,
                    'nombre'  => $a->archivo?->filename_original,
                    'peso'    => $a->archivo?->filesize_human,
                    'mime'    => $a->archivo?->mime_type,
                    'ruta'    => $a->archivo?->ruta
                        ? Storage::disk('minio')->temporaryUrl($a->archivo->ruta, now()->addHour())
                        : null,
                    'tipo'    => $a->tipo,
                    'firmado' => $a->firmado_digitalmente,
                    'usuario' => trim("{$a->user?->nombres} {$a->user?->paterno}"),
                ]),
                'historial'   => $ticket->historial->sortBy('created_at')->values()->map(fn($h) => [
                    'id'              => $h->id,
                    'estado_anterior' => $h->estadoAnterior?->codigo,
                    'estado_nuevo'    => $h->estadoNuevo?->codigo,
                    'usuario'         => trim("{$h->user?->nombres} {$h->user?->paterno}"),
                    'comentario'      => $h->comentario,
                    'es_conformidad'  => $h->es_conformidad,
                    'fecha'           => $h->created_at?->format('d/m/Y H:i'),
                ]),
            ],
        ]);
    }

    public function conformidad(Request $request, Ticket $ticket)
    {
        abort_if($ticket->solicitante_id !== Auth::id(), 403);
        abort_if($ticket->estado !== 'ATENDIDO', 422, 'El ticket no está en estado ATENDIDO.');

        $estadoAtendido = Estado::where('codigo', 'ATENDIDO')->firstOrFail();
        $estadoCerrado  = Estado::where('codigo', 'CERRADO')->firstOrFail();

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
