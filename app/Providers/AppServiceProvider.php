<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Crea el esquema PostgreSQL si no existe antes de cualquier operacion de BD.
        // El try-catch evita errores durante comandos donde la BD no esta disponible
        // (ej: composer package:discover, artisan config:cache, etc.).
        if (config('database.default') === 'pgsql') {
            try {
                $schema = config('database.connections.pgsql.search_path', 'public');
                DB::unprepared("CREATE SCHEMA IF NOT EXISTS \"{$schema}\"");
            } catch (\Throwable) {
                // BD no disponible, se ignorara hasta la proxima conexion exitosa
            }
        }
    }
}
