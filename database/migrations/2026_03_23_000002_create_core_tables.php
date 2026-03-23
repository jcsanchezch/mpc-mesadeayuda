<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependencias', function (Blueprint $table) {
            $table->id(); $table->string('nombre', 255);
            $table->string('abreviatura', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('origen_id')->nullable()->unique()->comment('ID original en la BD principal de origen');
            $table->timestamps(0);
        });

        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origen_id')->nullable()->unique()->comment('ID original en la BD principal de origen');
            $table->string('nombre', 255);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('origen_id')->nullable()->unique()->comment('ID original en la BD principal de origen');
            $table->foreignId('dependencia_id')->constrained('dependencias');
            $table->foreignId('cargo_id')->constrained('cargos');
            $table->string('dni', 10)->unique();
            $table->string('paterno', 100);
            $table->string('materno', 100);
            $table->string('nombres', 100);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('especialistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores');
            $table->boolean('vinculo_laboral')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('tipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300);
            $table->text('descripcion')->nullable();
            $table->boolean('disponible_solicitante')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('tipo_id')->constrained('tipos');
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('especialistas_servicios', function (Blueprint $table) {
            $table->foreignId('especialista_id')->constrained('especialistas')->cascadeOnDelete();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->unsignedTinyInteger('nivel')->default(2)->comment('Nivel de atención: 2 o 3');
            $table->boolean('activo')->default(true);
            $table->primary(['especialista_id', 'servicio_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('trabajador_id')->nullable()->after('usuario')
                ->constrained('trabajadores')->nullOnDelete();
            $table->unique('trabajador_id');
        });

        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique();
            $table->string('label', 100);
            $table->string('color', 10)->default('#000000');
            $table->boolean('es_inicio')->default(false)->comment('El ticket inicia con este estado');
            $table->boolean('es_fin')->default(false)->comment('El ticket finaliza con este estado');
            $table->string('actor', 15)->nullable()->comment('Quién debe actuar: ti | solicitante | null si es estado final');
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->foreignId('solicitante_id')->constrained('users');
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->foreignId('especialista_id')->nullable()->constrained('especialistas')->nullOnDelete();
            $table->string('estado', 30)->default('EN_ESPERA');
            // EN_ESPERA | ASIGNADO | PROGRAMADO | ATENDIENDO | INFORMACION | ATENDIDO | CANCELADO | CERRADO
            $table->string('asunto', 500);
            $table->text('descripcion');
            $table->text('resolucion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('fecha_inicio_atencion', 0)->nullable();
            $table->timestamp('fecha_resolucion', 0)->nullable();
            $table->boolean('es_padre')->default(true);
            $table->foreignId('ticket_padre_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->timestamps(0);

            $table->index('estado');
            $table->index('solicitante_id');
            $table->index('especialista_id');
            $table->index('ticket_padre_id');
        });


        Schema::create('tickets_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('estado_anterior_id')->nullable()->constrained('estados')->nullOnDelete();
            $table->foreignId('estado_nuevo_id')->constrained('estados');
            $table->foreignId('user_id')->constrained('users');
            $table->text('comentario')->nullable();
            $table->boolean('es_conformidad')->default(false)
                ->comment('El solicitante da conformidad y cierra el ticket');
            $table->timestamp('created_at', 0)->useCurrent();
        });

        Schema::create('archivos', function (Blueprint $table) {
            $table->id();
            $table->string('filename', 255);
            $table->string('filename_original', 255);
            $table->unsignedBigInteger('filesize');
            $table->string('filesize_human', 20);
            $table->string('hash', 64)->unique();
            $table->string('mime_type', 100)->nullable();
            $table->string('carpeta', 255);
            $table->string('ruta', 500);
            $table->timestamps(0);
        });

        Schema::create('formatos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('servicios')->cascadeOnDelete();
            $table->foreignId('archivo_id')->constrained('archivos');
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('tickets_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('archivo_id')->constrained('archivos');
            $table->foreignId('user_id')->constrained('users');
            // historial_id: entrada del historial a la que está asociado el archivo
            $table->foreignId('historial_id')->nullable()->constrained('tickets_historial')->nullOnDelete();
            // formato_origen_id: si este archivo es un formato completado y devuelto,
            // apunta al registro del formato original enviado por el especialista
            $table->foreignId('formato_id')->nullable()->constrained('formatos')->nullOnDelete();
            $table->string('tipo', 25)->default('adjunto');
            // adjunto            → archivo adjunto por el solicitante al crear/responder el ticket
            // formato            → plantilla enviada por el especialista para que el solicitante la complete
            // formato_completado → formato llenado, firmado digitalmente y devuelto por el solicitante
            $table->boolean('firmado_digitalmente')->default(false);
            $table->timestamps(0);

            $table->index('ticket_id');
            $table->index('tipo');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_archivos');
        Schema::dropIfExists('formatos');
        Schema::dropIfExists('archivos');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('estados');
        Schema::dropIfExists('especialistas_servicios');
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('tipos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('especialistas');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['trabajador_id']);
            $table->dropColumn('trabajador_id');
        });
        Schema::dropIfExists('trabajadores');
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('dependencias');
        Schema::dropIfExists('tickets_historial');
    }
};
