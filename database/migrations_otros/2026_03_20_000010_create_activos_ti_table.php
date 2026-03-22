<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_ti', function (Blueprint $table) {
            $table->bigIncrements('id_activo');
            $table->string('codigo_activo', 50)->unique();
            // 'computadora' | 'laptop' | 'impresora' | 'scanner' | 'switch' |
            // 'router' | 'servidor' | 'ups' | 'telefono_ip' | 'otro'
            $table->string('tipo', 50);
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 150)->nullable();
            $table->string('numero_serie', 100)->nullable()->unique();
            $table->unsignedBigInteger('id_area')->nullable();
            $table->unsignedBigInteger('id_trabajador_asignado')->nullable();
            // 'operativo' | 'en_mantenimiento' | 'de_baja' | 'en_almacen'
            $table->string('estado', 30)->default('operativo');
            $table->date('fecha_adquisicion')->nullable();
            $table->date('garantia_hasta')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_area')->references('id_area')->on('areas')->nullOnDelete();
            $table->foreign('id_trabajador_asignado')->references('id_trabajador')->on('trabajadores')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos_ti');
    }
};
