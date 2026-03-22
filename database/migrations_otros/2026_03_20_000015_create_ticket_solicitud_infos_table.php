<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_solicitud_infos', function (Blueprint $table) {
            $table->bigIncrements('id_solicitud_info');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_movimiento');
            $table->unsignedBigInteger('id_tecnico');
            $table->text('pregunta');
            // 'pendiente' | 'respondida' | 'vencida'
            $table->string('estado', 20)->default('pendiente');
            $table->text('respuesta')->nullable();
            $table->unsignedBigInteger('id_trabajador_responde')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamp('fecha_limite')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_movimiento')->references('id_movimiento')->on('ticket_movimientos');
            $table->foreign('id_tecnico')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_trabajador_responde')->references('id_trabajador')->on('trabajadores')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_solicitud_infos');
    }
};
