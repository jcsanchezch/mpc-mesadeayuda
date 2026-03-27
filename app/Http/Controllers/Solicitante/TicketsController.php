<?php

namespace App\Http\Controllers\Solicitante;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Canal;
use App\Models\Categoria;
use App\Models\Dependencia;
use App\Models\Estado;
use App\Models\Local;
use App\Models\Servicio;
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
            'servicio_id'    => ['nullable', 'integer', 'exists:servicios,id'],
            'dependencia_id' => ['nullable', 'integer', 'exists:dependencias,id'],
            'local_id'       => ['nullable', 'integer', 'exists:locales,id'],
            'asunto'         => ['required', 'string', 'max:500'],
            'celular'        => ['nullable', 'string', 'max:15'],
            'descripcion'    => ['nullable', 'string'],
            'archivos'       => ['nullable', 'array', 'max:5'],
            'archivos.*'     => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif'],
        ]);

        if ($validated['modo'] === '2') {
            $request->validate(['servicio_id' => ['required']]);
            $validated['asunto'] = Servicio::findOrFail($validated['servicio_id'])->nombre;
        }

        $year  = now()->year;
        $last  = Ticket::whereYear('created_at', $year)->orderByDesc('id')->value('codigo');
        $seq   = 1;
        if ($last && preg_match('/\d{4}-(\d+)/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }
        $codigo = sprintf('%d-%05d', $year, $seq);

        $estadoInicio = Estado::where('es_inicio', true)->firstOrFail();
        $canalMesa    = Canal::where('codigo', 'MESA_DE_SERVICIO')->firstOrFail();

        $ticket = Ticket::create([
            'codigo'           => $codigo,
            'solicitante_id'   => Auth::id(),
            'dependencia_id'   => $validated['dependencia_id'] ?? null,
            'local_id'         => $validated['local_id'] ?? null,
            'canal_id'         => $canalMesa->id,
            'servicio_id'      => $validated['servicio_id'] ?? null,
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
