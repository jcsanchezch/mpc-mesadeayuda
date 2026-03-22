<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicio_tecnico', function (Blueprint $table) {
            $table->unsignedBigInteger('id_servicio');
            $table->unsignedBigInteger('id_trabajador');

            $table->primary(['id_servicio', 'id_trabajador']);
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->cascadeOnDelete();
            $table->foreign('id_trabajador')->references('id_trabajador')->on('trabajadores')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_tecnico');
    }
};
