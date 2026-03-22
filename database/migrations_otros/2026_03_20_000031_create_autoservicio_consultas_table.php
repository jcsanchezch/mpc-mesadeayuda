<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autoservicio_consultas', function (Blueprint $table) {
            $table->bigIncrements('id_consulta');
            $table->unsignedBigInteger('id_trabajador');
            $table->unsignedBigInteger('id_conocimiento');
            $table->unsignedBigInteger('id_servicio')->nullable();
            // TRUE = el articulo no resolvio el problema y el usuario abrio un ticket
            $table->boolean('abrio_ticket')->default(false);
            $table->unsignedBigInteger('id_ticket_abierto')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_conocimiento')->references('id_conocimiento')->on('conocimientos');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->nullOnDelete();
            $table->foreign('id_ticket_abierto')->references('id_ticket')->on('tickets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autoservicio_consultas');
    }
};
