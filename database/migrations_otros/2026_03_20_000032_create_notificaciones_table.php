<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id_notificacion');
            $table->unsignedBigInteger('id_trabajador');
            $table->string('canal', 20)->default('correo');

            $table->unsignedBigInteger('id_ticket')->nullable();
            $table->unsignedBigInteger('id_movimiento_ticket')->nullable();

            // 'ticket_registrado' | 'ticket_asignado' | 'ticket_en_atencion' |
            // 'solicitud_info' | 'ticket_resuelto' | 'conformidad_pendiente' |
            // 'conformidad_automatica_pronto' | 'ticket_cerrado' |
            // 'aprobacion_requerida' | 'ticket_transferido'
            $table->string('tipo_evento', 50);

            $table->string('asunto', 300)->nullable();
            $table->text('cuerpo')->nullable();

            $table->string('estado', 20)->default('pendiente');
            $table->unsignedSmallInteger('intentos')->default(0);
            $table->timestamp('fecha_envio')->nullable();
            $table->text('error_detalle')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->nullOnDelete();
            $table->foreign('id_movimiento_ticket')->references('id_movimiento')->on('ticket_movimientos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
