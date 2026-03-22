<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feriados', function (Blueprint $table) {
            $table->bigIncrements('id_feriado');
            $table->date('fecha')->unique();
            $table->string('nombre', 150);
            // 'nacional' | 'regional' | 'local'
            $table->string('tipo', 20)->default('nacional');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
