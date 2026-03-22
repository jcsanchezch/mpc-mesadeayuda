<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extiende la tabla `users` de Laravel con datos institucionales.
     * Roles (solicitante, tecnico, supervisor, administrador) gestionados
     * por Spatie Laravel-Permission. Tokens por Sanctum.
     */
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->char('dni', 8)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 20)->nullable();
            $table->unsignedBigInteger('dependencia_id')->nullable();
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('dependencia_id')->references('id')->on('dependencias')->nullOnDelete();
            $table->foreign('cargo_id')->references('id')->on('cargos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
