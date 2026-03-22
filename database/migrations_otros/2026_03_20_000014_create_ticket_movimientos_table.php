<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_movimientos', function (Blueprint $table) {
            $table->bigIncrements('id_movimiento');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_trabajador');

            // registro | clasificacion | aprobacion | rechazo | asignacion |
            // inicio_atencion | avance | solicitud_info | respuesta_info |
            // transferencia | resolucion | conformidad | conformidad_auto |
            // reapertura | cancelacion | nota_interna
            $table->string('tipo_movimiento', 30);

            $table->string('estado_anterior', 30)->nullable();
            $table->string('estado_nuevo', 30)->nullable();
            $table->unsignedBigInteger('id_tecnico_destino')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('id_plantilla')->nullable();
            $table->boolean('visible_solicitante')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_tecnico_destino')->references('id_trabajador')->on('trabajadores')->nullOnDelete();
            $table->foreign('id_plantilla')->references('id_plantilla')->on('plantilla_respuestas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_movimientos');
    }
};
