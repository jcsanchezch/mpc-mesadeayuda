<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_logs', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->string('tabla', 100);
            // 'INSERT' | 'UPDATE' | 'DELETE'
            $table->string('operacion', 10);
            $table->unsignedBigInteger('id_registro');
            // Trabajador de la aplicacion
            $table->unsignedBigInteger('id_trabajador_app')->nullable();
            $table->string('usuario_bd', 100);
            // IP del cliente (string para compatibilidad con MySQL/PG)
            $table->string('ip_cliente', 45)->nullable();
            $table->json('valores_antes')->nullable();
            $table->json('valores_despues')->nullable();
            $table->string('hash_integridad', 64)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_trabajador_app')->references('id_trabajador')->on('trabajadores')->nullOnDelete();

            $table->index('tabla');
            $table->index('operacion');
            $table->index('id_registro');
            $table->index('id_trabajador_app');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_logs');
    }
};
