<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    public function run(): void
    {
        // actor: 'ti' = personal de TI debe actuar | 'solicitante' = solicitante debe actuar | null = estado final
        $estados = [
            ['nombre' => 'EN_ESPERA',   'label' => 'En Espera',   'color' => '#a16207', 'es_inicio' => true,  'es_fin' => false, 'actor' => 'ti',          'activo' => true],
            ['nombre' => 'ASIGNADO',    'label' => 'Asignado',    'color' => '#7e22ce', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'ti',          'activo' => true],
            ['nombre' => 'PROGRAMADO',  'label' => 'Programado',  'color' => '#4338ca', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'ti',          'activo' => true],
            ['nombre' => 'ATENDIENDO',  'label' => 'Atendiendo',  'color' => '#1d4ed8', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'ti',          'activo' => true],
            ['nombre' => 'INFORMACION', 'label' => 'Información', 'color' => '#c2410c', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'solicitante', 'activo' => true],
            ['nombre' => 'ATENDIDO',    'label' => 'Atendido',    'color' => '#047857', 'es_inicio' => false, 'es_fin' => false, 'actor' => 'solicitante', 'activo' => true],
            ['nombre' => 'CANCELADO',   'label' => 'Cancelado',   'color' => '#b91c1c', 'es_inicio' => false, 'es_fin' => true,  'actor' => null,          'activo' => true],
            ['nombre' => 'CERRADO',     'label' => 'Cerrado',     'color' => '#374151', 'es_inicio' => false, 'es_fin' => true,  'actor' => null,          'activo' => true],
        ];

        foreach ($estados as $estado) {
            DB::table('estados')->updateOrInsert(
                ['nombre' => $estado['nombre']],
                $estado
            );
        }
    }
}
