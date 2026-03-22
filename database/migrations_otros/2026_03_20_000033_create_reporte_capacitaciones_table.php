<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte_capacitaciones', function (Blueprint $table) {
            $table->bigIncrements('id_reporte');
            $table->unsignedSmallInteger('anio');
            // 1-12 para mensual, NULL para anual
            $table->unsignedTinyInteger('mes')->nullable();
            $table->unsignedBigInteger('id_area')->nullable();
            $table->unsignedBigInteger('id_servicio')->nullable();

            $table->unsignedInteger('total_tickets')->default(0);
            $table->unsignedInteger('tickets_por_conocimiento_ti')->default(0);
            $table->unsignedInteger('tickets_prevenibles')->default(0);

            // Array JSON de strings con temas frecuentes
            $table->json('temas_frecuentes')->nullable();
            $table->decimal('nivel_conocimiento_promedio', 3, 2)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('id_generado_por')->nullable();

            $table->unique(['anio', 'mes', 'id_area', 'id_servicio'], 'reporte_periodo_unico');

            $table->foreign('id_area')->references('id_area')->on('areas')->nullOnDelete();
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->nullOnDelete();
            $table->foreign('id_generado_por')->references('id_trabajador')->on('trabajadores')->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_capacitaciones');
    }
};
