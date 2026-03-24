<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('abreviatura', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('origen_id')->nullable()->unique()->comment('ID original en la BD principal de origen');
            // responsable_id se añade después de crear trabajadores (dependencia circular)
            $table->timestamps(0);
        });

        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('direccion', 200)->nullable();
            $table->boolean('principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dependencia_id')->constrained('dependencias');
            $table->foreignId('local_id')->nullable()->constrained('locales')->nullOnDelete();
            $table->string('dni', 10)->unique();
            $table->string('paterno', 100);
            $table->string('materno', 100);
            $table->string('nombres', 100);
            $table->string('celular', 15)->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('origen_id')->nullable()->unique()->comment('ID original en la BD principal de origen');
            $table->timestamps(0);
        });

        // Referencia circular resuelta: dependencias ← trabajadores
        Schema::table('dependencias', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()->after('activo')
                ->constrained('trabajadores')->nullOnDelete();
        });

        Schema::create('especialistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadores');
            $table->boolean('vinculo_laboral')->default(false);
            $table->boolean('voluntario')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('tipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 300);
            $table->string('label', 300);
            $table->text('descripcion')->nullable();
            $table->boolean('disponible_al_solicitante')->default(true);
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


        Schema::create('niveles', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nivel')->unique()
                  ->comment('Tier ITIL 4 — 0: Autoservicio, 1: Mesa de Servicios, 2: Soporte Técnico, 3: Soporte Experto, 4: Proveedor Externo');
            $table->string('codigo', 10)->unique()
                  ->comment('Código corto: N0, N1, N2, N3, N4');
            $table->string('label', 150);
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
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('nivel_id')->constrained('niveles');
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
            $table->string('codigo', 30)->unique();
            $table->string('label', 100);
            $table->string('text_color', 100)->default('text-gray-700');
            $table->string('bg_color', 100)->default('text-gray-100');
            $table->boolean('es_inicio')->default(false)->comment('El ticket inicia con este estado');
            $table->boolean('es_fin')->default(false)->comment('El ticket finaliza con este estado');
            $table->string('actor', 15)->nullable()->comment('Quién debe actuar: ti | solicitante | null si es estado final');
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('prioridades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('label', 100);
            $table->string('text_color', 50)->default('text-gray-500');
            $table->string('bg_color', 50)->default('bg-gray-100');
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('canales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('label', 100);
            $table->boolean('es_aplicacion')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('dificultades', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nivel')->unique()->comment('Orden ascendente: 1=más simple, 5=más complejo');
            $table->string('codigo', 30)->unique();
            $table->string('label', 100);
            $table->string('color', 10)->default('#6b7280');
            $table->boolean('activo')->default(true);
            $table->timestamps(0);
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();

            $table->foreignId('solicitante_id')->constrained('trabajadores');
            $table->foreignId('dependencia_id')->constrained('dependencias');
            $table->foreignId('local_id')->constrained('locales');
            $table->foreignId('canal_id')->nullable()->constrained('canales')->nullOnDelete();

            $table->foreignId('prioridad_id')->nullable()->constrained('prioridades')->nullOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->foreignId('especialista_id')->nullable()->constrained('especialistas')->nullOnDelete();
            $table->boolean('servicio_directo')->default(false)->comment('El asunto fue tomado del nombre del servicio, no escrito manualmente');

            $table->foreignId('dificultad_id')->nullable()->constrained('dificultades')->nullOnDelete();

            $table->string('estado', 30)->default('EN_ESPERA');
            $table->string('asunto', 500);
            $table->string('celular', 15)->nullable();
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
            $table->string('hash', 64);
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
        Schema::dropIfExists('tickets_historial');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('canales');
        Schema::dropIfExists('dificultades');
        Schema::dropIfExists('prioridades');
        Schema::dropIfExists('estados');
        Schema::dropIfExists('especialistas_servicios');
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('niveles');
        Schema::dropIfExists('tipos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('especialistas');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['trabajador_id']);
            $table->dropColumn('trabajador_id');
        });
        // Soltar FK circular antes de dropear trabajadores y dependencias
        Schema::table('dependencias', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn('responsable_id');
        });
        Schema::dropIfExists('trabajadores');
        Schema::dropIfExists('locales');
        Schema::dropIfExists('dependencias');
    }
};
