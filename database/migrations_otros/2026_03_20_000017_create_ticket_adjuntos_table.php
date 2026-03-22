<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_adjuntos', function (Blueprint $table) {
            $table->bigIncrements('id_adjunto');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_movimiento')->nullable();
            $table->unsignedBigInteger('id_solicitud_info')->nullable();
            $table->unsignedBigInteger('id_trabajador');
            $table->string('nombre_archivo', 255);
            $table->string('ruta_almacen', 500);
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->string('tipo_mime', 100)->nullable();
            // 'todos' | 'solo_oti'
            $table->string('visibilidad', 20)->default('todos');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_movimiento')->references('id_movimiento')->on('ticket_movimientos')->nullOnDelete();
            $table->foreign('id_solicitud_info')->references('id_solicitud_info')->on('ticket_solicitud_infos')->nullOnDelete();
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_adjuntos');
    }
};
