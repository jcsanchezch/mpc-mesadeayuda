<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_pausa_slas', function (Blueprint $table) {
            $table->bigIncrements('id_pausa');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_movimiento_inicio');
            $table->unsignedBigInteger('id_movimiento_fin')->nullable();
            // 'pendiente_info' | 'fuera_horario' | 'feriado'
            $table->string('motivo_pausa', 30);
            $table->timestamp('fecha_inicio_pausa');
            $table->timestamp('fecha_fin_pausa')->nullable();
            $table->integer('minutos_pausados')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_movimiento_inicio')->references('id_movimiento')->on('ticket_movimientos');
            $table->foreign('id_movimiento_fin')->references('id_movimiento')->on('ticket_movimientos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_pausa_slas');
    }
};
