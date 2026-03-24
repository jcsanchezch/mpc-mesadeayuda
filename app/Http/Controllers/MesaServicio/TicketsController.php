<?php

namespace App\Http\Controllers\MesaServicio;

use App\Http\Controllers\Controller;
use App\Models\Archivo;
use App\Models\Canal;
use App\Models\Categoria;
use App\Models\Dependencia;
use App\Models\Local;
use App\Models\Estado;
use App\Models\Prioridad;
use App\Models\Servicio;
use App\Models\Ticket;
use App\Models\TicketArchivo;
use App\Models\TicketHistorial;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TicketsController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with([
            'servicio.categoria',
            'solicitante',
            'prioridad',
            'historial.estadoNuevo',
            'historial.user',
            'archivos.archivo',
        ])
            ->whereNull('especialista_id')
            ->latest()
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'codigo' => $t->codigo,
                'dni' => $t->solicitante?->dni,
                'solicitante' => trim("{$t->solicitante?->paterno} {$t->solicitante?->materno} {$t->solicitante?->nombres}"),
                'celular' => $t->celular,
                'categoria' => $t->servicio?->categoria?->nombre,
                'servicio' => $t->servicio?->nombre,
                'asunto' => $t->asunto,
                'descripcion' => $t->descripcion,
                'prioridad' => $t->prioridad?->nombre,
                'estado' => $t->estado,
                'fecha' => $t->created_at?->format('d/m/Y H:i'),
                'archivos' => $t->archivos->map(fn($a) => [
                    'id' => $a->id,
                    'nombre' => $a->archivo?->filename_original,
                    'peso' => $a->archivo?->filesize_human,
                    'ruta' => $a->archivo?->ruta
                        ? Storage::disk('minio')->temporaryUrl($a->archivo->ruta, now()->addHour())
                        : null,
                    'tipo' => $a->tipo,
                ]),
                'historial' => $t->historial->sortBy('created_at')->values()->map(fn($h) => [
                    'id' => $h->id,
                    'estado_anterior' => $h->estadoAnterior?->nombre,
                    'estado_nuevo' => $h->estadoNuevo?->nombre,
                    'usuario' => trim("{$h->user?->nombres} {$h->user?->paterno}"),
                    'comentario' => $h->comentario,
                    'es_conformidad' => $h->es_conformidad,
                    'fecha' => $h->created_at?->format('d/m/Y H:i'),
                ]),
            ]);

        $estados     = Estado::where('activo', true)->get(['nombre', 'label', 'text_color', 'bg_color']);
        $prioridades = Prioridad::where('activo', true)->get(['nombre', 'label', 'text_color', 'bg_color']);

        return Inertia::render('MesaServicio/Tickets/Index', [
            'tickets'     => $tickets,
            'estados'     => $estados,
            'prioridades' => $prioridades,
        ]);
    }

    public function crearVista()
    {
        $canales = Canal::where('activo', true)
            ->orderBy('label')
            ->get(['id', 'nombre', 'label']);

        $categorias = Categoria::with(['servicios' => function ($q) {
            $q->where('activo', true)
                ->with(['formatos.archivo'])
                ->orderBy('nombre');
        }])
            ->whereHas('servicios', fn($q) => $q->where('activo', true))
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $dependencias = Dependencia::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $locales = Local::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'direccion']);

        return Inertia::render('MesaServicio/Tickets/Crear', [
            'canales'      => $canales,
            'categorias'   => $categorias,
            'dependencias' => $dependencias,
            'locales'      => $locales,
        ]);
    }

    public function crearTicket(Request $request)
    {
        $validated = $request->validate([
            'trabajador_id'  => ['required', 'integer', 'exists:trabajadores,id'],
            'dependencia_id' => ['required', 'integer', 'exists:dependencias,id'],
            'local_id'       => ['required', 'integer', 'exists:locales,id'],
            'canal_id'       => ['required', 'integer', 'exists:canales,id'],
            'modo' => ['required', 'in:1,2'],
            'servicio_id' => ['nullable', 'integer', 'exists:servicios,id'],
            'asunto' => ['required_if:modo,1', 'nullable', 'string', 'max:500'],
            'celular' => ['required', 'string', 'max:15'],
            'descripcion' => ['required', 'string'],
            'archivos' => ['nullable', 'array', 'max:10'],
            'archivos.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif'],
        ]);

        if ($validated['modo'] === '2') {
            $request->validate(['servicio_id' => ['required']]);
            $validated['asunto'] = Servicio::findOrFail($validated['servicio_id'])->nombre;
        }

        // Convertir string vacío a null para servicio_id (viene así desde FormData)
        if (($validated['servicio_id'] ?? null) === '') {
            $validated['servicio_id'] = null;
        }

        $trabajador = Trabajador::findOrFail($validated['trabajador_id']);

        $year = now()->year;
        $last = Ticket::whereYear('created_at', $year)->orderByDesc('id')->value('codigo');
        $seq = 1;
        if ($last && preg_match('/\d{4}-(\d+)/', $last, $m)) {
            $seq = (int)$m[1] + 1;
        }
        $codigo = sprintf('%d-%05d', $year, $seq);

        $estadoInicio = Estado::where('es_inicio', true)->firstOrFail();

        $ticket = Ticket::create([
            'codigo' => $codigo,
            'solicitante_id'  => $trabajador->id,
            'dependencia_id'  => $validated['dependencia_id'],
            'local_id'        => $validated['local_id'],
            'canal_id'        => $validated['canal_id'],
            'servicio_id' => $validated['servicio_id'] ?? null,
            'estado' => $estadoInicio->nombre,
            'asunto' => $validated['asunto'],
            'celular' => $validated['celular'],
            'descripcion' => $validated['descripcion'],
        ]);

        TicketHistorial::create([
            'ticket_id' => $ticket->id,
            'estado_anterior_id' => null,
            'estado_nuevo_id' => $estadoInicio->id,
            'user_id' => Auth::id(),
            'comentario' => 'Ticket creado por Mesa de Servicio.',
            'es_conformidad' => false,
            'created_at' => now(),
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $file) {
                $carpeta = "tickets/{$codigo}";
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $ruta = "{$carpeta}/{$filename}";

                Storage::disk('minio')->putFileAs($carpeta, $file, $filename, 'private');

                $bytes = $file->getSize();
                $archivo = Archivo::create([
                    'filename' => $filename,
                    'filename_original' => $file->getClientOriginalName(),
                    'filesize' => $bytes,
                    'filesize_human' => $this->humanSize($bytes),
                    'hash' => hash_file('sha256', $file->getRealPath()),
                    'mime_type' => $file->getMimeType(),
                    'carpeta' => $carpeta,
                    'ruta' => $ruta,
                ]);

                TicketArchivo::create([
                    'ticket_id' => $ticket->id,
                    'archivo_id' => $archivo->id,
                    'user_id' => Auth::id(),
                    'tipo' => 'adjunto',
                    'firmado_digitalmente' => false,
                ]);
            }
        }

        return redirect()->route('mesadeayuda.tickets.index')
            ->with('success', "Ticket {$codigo} creado correctamente.");
    }

    public function buscarTrabajador(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }

        $qNorm = Str::ascii(mb_strtoupper($q));

        $resultados = Trabajador::with(['dependencia', 'local'])
            ->where('activo', true)
            ->where(function ($query) use ($qNorm) {

                $query->whereRaw("
                    translate(upper(dni || ' ' || paterno || ' ' || materno || ' ' || nombres), 'áéíóúÁÉÍÓÚñÑ', 'aeiouAEIOUnN')
                    LIKE translate(upper(?), 'áéíóúÁÉÍÓÚñÑ', 'aeiouAEIOUnN') ", ["%$qNorm%"]);

            })
            ->orderBy('paterno')->orderBy('materno')->orderBy('nombres')
            ->limit(20)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'label' => "{$t->dni} {$t->paterno} {$t->materno} {$t->nombres}",
                'celular' => $t->celular,
                'dependencia_id' => $t->dependencia?->id,
                'local_id' => $t->local?->id,
            ]);

        return response()->json($resultados);
    }

    private function humanSize(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) return "{$bytes} {$unit}";
            $bytes = (int)round($bytes / 1024);
        }
        return "{$bytes} TB";
    }
}
