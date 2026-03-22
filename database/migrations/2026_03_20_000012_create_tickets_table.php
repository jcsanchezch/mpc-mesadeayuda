<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id_ticket');
            // Codigo legible: TKT-YYYY-NNNNN
            $table->string('codigo', 20)->unique();

            // Nullable: el usuario crea el ticket y el agente clasifica después
            $table->unsignedBigInteger('id_servicio')->nullable();
            $table->unsignedBigInteger('id_solicitante');
            $table->unsignedBigInteger('id_agente_mesa')->nullable();
            $table->unsignedBigInteger('id_tecnico_asignado')->nullable();
            $table->unsignedBigInteger('id_sla')->nullable();
            $table->unsignedBigInteger('id_activo')->nullable();

            // nuevo | en_clasificacion | pendiente_aprobacion | en_atencion |
            // pendiente_info | transferido | resuelto | cerrado | cancelado | rechazado
            $table->string('estado', 30)->default('nuevo');

            $table->string('titulo', 200);
            $table->text('descripcion');
            // 'portal' | 'correo' | 'presencial' | 'telefono'
            $table->string('canal_ingreso', 30)->default('portal');

            // Clasificado por el agente de mesa: 'solicitud' | 'incidente'
            $table->string('clasificacion', 30)->nullable();
            // 1=Critico | 2=Alto | 3=Medio | 4=Bajo
            $table->unsignedTinyInteger('prioridad')->nullable();
            $table->string('urgencia', 20)->nullable();
            $table->string('impacto', 20)->nullable();

            $table->timestamp('fecha_limite_respuesta')->nullable();
            $table->timestamp('fecha_limite_resolucion')->nullable();
            $table->timestamp('fecha_primera_respuesta')->nullable();
            $table->timestamp('fecha_inicio_atencion')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamp('fecha_limite_conformidad')->nullable();
            $table->timestamp('fecha_conformidad')->nullable();
            $table->boolean('conformidad_automatica')->default(false);
            $table->timestamp('fecha_cierre')->nullable();

            $table->unsignedSmallInteger('cantidad_reaperturas')->default(0);
            $table->boolean('reabierto')->default(false);

            $table->unsignedTinyInteger('calificacion')->nullable();
            $table->text('comentario_usuario')->nullable();

            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->nullOnDelete();
            $table->foreign('id_solicitante')->references('id_trabajador')->on('trabajadores');
            $table->foreign('id_agente_mesa')->references('id_trabajador')->on('trabajadores')->nullOnDelete();
            $table->foreign('id_tecnico_asignado')->references('id_trabajador')->on('trabajadores')->nullOnDelete();
            $table->foreign('id_sla')->references('id_sla')->on('slas')->nullOnDelete();
            $table->foreign('id_activo')->references('id_activo')->on('activos_ti')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
