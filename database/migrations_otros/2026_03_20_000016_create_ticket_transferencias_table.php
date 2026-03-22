<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_transferencias', function (Blueprint $table) {
            $table->bigIncrements('id_transferencia');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_movimiento');
            $table->unsignedBigInteger('id_tecnico_origen');
            $table->unsignedBigInteger('id_tecnico_destino');
            $table->text('motivo');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_movimiento')->references('id_movimiento')->on('ticket_movimientos');
            $table->foreign('id_tecnico_origen')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_tecnico_destino')->references('id_trabajador')->on('trabajadores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_transferencias');
    }
};
