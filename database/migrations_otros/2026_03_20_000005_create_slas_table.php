<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slas', function (Blueprint $table) {
            $table->bigIncrements('id_sla');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            // 1=Critico | 2=Alto | 3=Medio | 4=Bajo
            $table->unsignedTinyInteger('prioridad');
            $table->decimal('tiempo_respuesta_h', 5, 2);
            $table->decimal('tiempo_resolucion_h', 5, 2);
            $table->unsignedTinyInteger('dias_conformidad_automatica')->default(3);
            $table->boolean('activo')->default(true);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slas');
    }
};
