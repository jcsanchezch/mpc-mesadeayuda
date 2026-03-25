<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Local;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PerfilController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user()->load('trabajador');

        $locales = Local::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $dependencias = Dependencia::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'abreviatura']);

        return Inertia::render('Perfil/Index', [
            'celular'        => $user->trabajador?->celular,
            'local_id'       => $user->trabajador?->local_id,
            'dependencia_id' => $user->trabajador?->dependencia_id,
            'locales'        => $locales,
            'dependencias'   => $dependencias,
            'status'         => session('status'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'celular'        => ['nullable', 'string', 'max:15'],
            'local_id'       => ['nullable', 'integer', 'exists:locales,id'],
            'dependencia_id' => ['nullable', 'integer', 'exists:dependencias,id'],
        ]);

        $trabajador = $request->user()->trabajador;

        if ($trabajador) {
            $trabajador->update([
                'celular'        => $validated['celular'],
                'local_id'       => $validated['local_id'] ?? null,
                'dependencia_id' => $validated['dependencia_id'] ?? null,
            ]);
        }

        return back()->with('status', 'Perfil actualizado correctamente.');
    }
}
