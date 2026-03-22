<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_conformidades', function (Blueprint $table) {
            $table->bigIncrements('id_conformidad');
            $table->unsignedBigInteger('id_ticket')->unique();
            $table->unsignedBigInteger('id_movimiento');
            // 'manual' | 'automatica'
            $table->string('tipo', 20)->default('manual');
            // 'conforme' | 'no_conforme'
            $table->string('resultado', 20);
            $table->text('motivo_disconformidad')->nullable();
            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->text('comentario')->nullable();
            $table->unsignedBigInteger('id_trabajador')->nullable();
            $table->timestamp('fecha_conformidad')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_movimiento')->references('id_movimiento')->on('ticket_movimientos');
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores')->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_conformidades');
    }
};
