<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plantilla_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id_plantilla');
            $table->unsignedBigInteger('id_servicio')->nullable();
            $table->unsignedBigInteger('id_autor')->nullable();
            $table->string('nombre', 200);
            // 'solicitud_info' | 'avance' | 'resolucion' | 'cualquiera'
            $table->string('tipo_movimiento', 30)->default('cualquiera');
            $table->text('contenido');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->nullOnDelete();
            $table->foreign('id_autor')->references('id_trabajador')->on('trabajadores')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_respuestas');
    }
};
