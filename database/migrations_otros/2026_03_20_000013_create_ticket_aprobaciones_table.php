<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_aprobaciones', function (Blueprint $table) {
            $table->bigIncrements('id_aprobacion');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('id_aprobador');
            // 'jefe_area' | 'supervisor_oti'
            $table->string('rol_aprobador', 30);
            $table->unsignedSmallInteger('orden')->default(1);
            // 'pendiente' | 'aprobado' | 'rechazado'
            $table->string('decision', 20)->default('pendiente');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_decision')->nullable();
            $table->timestamp('fecha_limite')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_aprobador')->references('id_trabajador')->on('trabajadores');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_aprobaciones');
    }
};
