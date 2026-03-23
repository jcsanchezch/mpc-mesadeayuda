<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OmitirSesionSafeLine
{

    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent();

        // Detectar SafeLine WAF
        if ($userAgent && str_contains($userAgent, 'SafeLine')) {
            // Retornar respuesta inmediatamente sin pasar por más middleware
            return response('OK', 200)
                ->header('Content-Type', 'text/plain');
        }

        return $next($request);
    }
}
