<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            // 'solicitud' | 'incidente'
            $table->string('tipo', 30)->default('solicitud');
            $table->boolean('requiere_aprobacion')->default(false);
            // 'jefe_area' | 'supervisor_oti' | 'ambos'
            $table->string('tipo_aprobador', 30)->nullable();
            $table->boolean('permite_autoservicio')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('id_categoria')->references('id_categoria')->on('categoria_servicios');
            $table->foreign('id_sla_defecto')->references('id_sla')->on('slas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
