<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conocimientos', function (Blueprint $table) {
            $table->bigIncrements('id_conocimiento');
            $table->string('titulo', 300);
            $table->text('descripcion_problema');
            $table->text('solucion');
            $table->text('pasos_solucion')->nullable();
            $table->unsignedBigInteger('id_servicio')->nullable();
            $table->unsignedBigInteger('id_autor');
            $table->unsignedBigInteger('id_ticket')->nullable();
            $table->string('estado', 20)->default('borrador');
            $table->boolean('permite_autoservicio')->default(false);
            $table->unsignedInteger('visitas')->default(0);
            $table->unsignedInteger('util_votos')->default(0);
            $table->unsignedInteger('no_util_votos')->default(0);
            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->nullOnDelete();
            $table->foreign('id_autor')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_ticket')->references('id_ticket')->on('tickets')->nullOnDelete();
        });

        // FK diferida: ticket_cierres -> conocimientos
        Schema::table('ticket_cierres', function (Blueprint $table) {
            $table->foreign('id_conocimiento_usado')->references('id_conocimiento')->on('conocimientos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_cierres', function (Blueprint $table) {
            $table->dropForeign(['id_conocimiento_usado']);
        });
        Schema::dropIfExists('conocimientos');
    }
};
