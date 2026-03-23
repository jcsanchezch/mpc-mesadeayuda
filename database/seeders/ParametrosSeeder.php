<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametrosSeeder extends Seeder
{
    public function run(): void
    {
        // ── Estados ───────────────────────────────────────────────────────────
        // actor: 'ti' = personal TI debe actuar | 'solicitante' = solicitante debe actuar | null = estado final
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

        foreach ($estados as $e) {
            DB::table('estados')->updateOrInsert(['nombre' => $e['nombre']], $e);
        }

        // ── Canales ───────────────────────────────────────────────────────────
        $canales = [
            ['nombre' => 'MESA_DE_AYUDA', 'label' => 'Mesa de Servicio',               'activo' => true],
            ['nombre' => 'CORREO',        'label' => 'Correo Electrónico',             'activo' => true],
            ['nombre' => 'SGD',           'label' => 'Sistema de Gestión Documental',  'activo' => true],
            ['nombre' => 'LLAMADA',       'label' => 'Llamada Telefónica',             'activo' => true],
            ['nombre' => 'PRESENCIAL',    'label' => 'Presencial',                     'activo' => true],
        ];

        foreach ($canales as $c) {
            DB::table('canales')->updateOrInsert(['nombre' => $c['nombre']], $c);
        }

        // ── Prioridades ───────────────────────────────────────────────────────
        $prioridades = [
            ['nombre' => 'NORMAL',      'label' => 'Normal',      'activo' => true],
            ['nombre' => 'URGENTE',     'label' => 'Urgente',     'activo' => true],
            ['nombre' => 'MUY_URGENTE', 'label' => 'Muy Urgente', 'activo' => true],
        ];

        foreach ($prioridades as $p) {
            DB::table('prioridades')->updateOrInsert(['nombre' => $p['nombre']], $p);
        }

        // ── Dificultades ──────────────────────────────────────────────────────
        $dificultades = [
            ['nivel' => 1, 'nombre' => 'TRIVIAL',      'label' => 'Trivial',      'color' => '#6b7280', 'activo' => true],
            ['nivel' => 2, 'nombre' => 'SIMPLE',       'label' => 'Simple',       'color' => '#22c55e', 'activo' => true],
            ['nivel' => 3, 'nombre' => 'MODERADO',     'label' => 'Moderado',     'color' => '#3b82f6', 'activo' => true],
            ['nivel' => 4, 'nombre' => 'COMPLEJO',     'label' => 'Complejo',     'color' => '#f97316', 'activo' => true],
            ['nivel' => 5, 'nombre' => 'MUY_COMPLEJO', 'label' => 'Muy Complejo', 'color' => '#ef4444', 'activo' => true],
        ];

        foreach ($dificultades as $d) {
            DB::table('dificultades')->updateOrInsert(['nombre' => $d['nombre']], $d);
        }

        // ── Niveles ITIL 4 ────────────────────────────────────────────────────
        $niveles = [
            [
                'nivel'       => 0,
                'nombre'      => 'N0',
                'label'       => 'Autoservicio',
                'descripcion' => 'El usuario resuelve el incidente o solicitud por su propia cuenta mediante la base de conocimiento, FAQs o herramientas de autoservicio, sin intervención del equipo de TI.',
                'activo'      => true,
            ],
            [
                'nivel'       => 1,
                'nombre'      => 'N1',
                'label'       => 'Mesa de Servicios',
                'descripcion' => 'Primer punto de contacto. Atiende incidentes y solicitudes de baja complejidad: restablecimiento de contraseñas, consultas generales, enrutamiento y registro de tickets. Resuelve la mayoría de los casos.',
                'activo'      => true,
            ],
            [
                'nivel'       => 2,
                'nombre'      => 'N2',
                'label'       => 'Soporte Técnico',
                'descripcion' => 'Especialistas técnicos con conocimiento profundo en áreas específicas. Atiende los casos que N1 no pudo resolver: configuraciones, diagnósticos avanzados e incidentes de mediana complejidad.',
                'activo'      => true,
            ],
            [
                'nivel'       => 3,
                'nombre'      => 'N3',
                'label'       => 'Soporte Experto',
                'descripcion' => 'Expertos senior, arquitectos de soluciones o desarrolladores. Gestiona problemas de alta complejidad, errores de software/infraestructura, cambios estructurales y análisis de causa raíz (RCA).',
                'activo'      => true,
            ],
            [
                'nivel'       => 4,
                'nombre'      => 'N4',
                'label'       => 'Proveedor Externo',
                'descripcion' => 'Soporte brindado por fabricantes, proveedores de software o hardware, o terceros especializados. Se activa cuando la solución está fuera del alcance interno de la organización.',
                'activo'      => true,
            ],
        ];

        foreach ($niveles as $n) {
            DB::table('niveles')->updateOrInsert(['nombre' => $n['nombre']], $n);
        }
    }
}
