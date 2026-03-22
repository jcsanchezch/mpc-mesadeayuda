<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FK id_conocimiento_usado se agrega en 2026_03_20_000030_create_conocimientos_table.php
     * una vez que la tabla conocimientos existe.
     */
    public function up(): void
    {
        Schema::create('ticket_cierres', function (Blueprint $table) {
            $table->bigIncrements('id_cierre');
            $table->unsignedBigInteger('id_ticket')->unique();
            $table->unsignedBigInteger('id_tecnico');

            // 'conocimiento_ti' | 'hardware' | 'software' | 'configuracion'
            // | 'red' | 'acceso' | 'proceso' | 'otro'
            $table->string('categoria_causa', 50);
            $table->string('subcategoria_causa', 100)->nullable();
            $table->text('descripcion_causa');
            $table->text('solucion_aplicada');
            $table->integer('tiempo_resolucion_minutos')->nullable();
            $table->integer('minutos_pausados_total')->default(0);
            // FK a conocimientos — se agrega tras crear esa tabla
            $table->unsignedBigInteger('id_conocimiento_usado')->nullable();

            // 1=Muy bajo | 2=Bajo | 3=Medio | 4=Alto | 5=Experto
            $table->unsignedTinyInteger('nivel_conocimiento_ti')->nullable();
            $table->boolean('prevenible_con_capacitacion')->default(false);
            $table->string('tema_capacitacion_sugerido', 200)->nullable();
            $table->boolean('genera_articulo_conocimiento')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->cascadeOnDelete();
            $table->foreign('id_tecnico')->references('id_trabajador')->on('trabajadores');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_cierres');
    }
};
