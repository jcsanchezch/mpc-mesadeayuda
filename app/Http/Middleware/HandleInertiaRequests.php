<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $secciones = [];
        if ($user) {
            foreach (['mis_tickets', 'mesa_servicio', 'reportes', 'admin'] as $permiso) {
                if ($user->hasPermissionTo($permiso, 'web')) {
                    $secciones[] = $permiso;
                }
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user?->only('name', 'email'),
                'secciones' => $secciones,
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
