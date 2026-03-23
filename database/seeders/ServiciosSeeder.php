<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiciosSeeder extends Seeder
{
    public function run(): void
    {
        // ── TIPOS ─────────────────────────────────────────────────────────────
        $tipos = [
            ['nombre' => 'Solicitud', 'disponible_solicitante' => true,  'activo' => true],
            ['nombre' => 'Incidente', 'disponible_solicitante' => true,  'activo' => true],
            ['nombre' => 'Cambio',    'disponible_solicitante' => false, 'activo' => true],
            ['nombre' => 'Evento',    'disponible_solicitante' => false, 'activo' => true],
            ['nombre' => 'Problema',  'disponible_solicitante' => false, 'activo' => true],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos')->insertOrIgnore($tipo);
        }

        $tipoSolicitudId = DB::table('tipos')->where('nombre', 'Solicitud')->value('id');

        // ── CATEGORIAS Y SERVICIOS ────────────────────────────────────────────
        $catalogo = [
            'Servicios de Estación de Trabajo' => [
                'Provisión de equipo de cómputo',
                'Instalación o actualización de software',
                'Reparación o reemplazo de equipo',
                'Configuración de impresora o escáner',
            ],
            'Servicios de Comunicación' => [
                'Creación de cuenta de correo institucional',
                'Acceso a plataforma de videoconferencia',
                'Acceso a directorio telefónico interno (VoIP)',
                'Acceso a la intranet municipal',
            ],
            'Servicios de Conectividad' => [
                'Acceso a internet',
                'Acceso a la red institucional (LAN/WiFi)',
                'Acceso remoto a la red (VPN)',
            ],
            'Servicios de Acceso e Identidad' => [
                'Creación de cuenta de usuario',
                'Restablecimiento de contraseña',
                'Solicitud de acceso a sistema o aplicativo',
                'Modificación o baja de cuenta de usuario',
            ],
            'Servicios de Almacenamiento y Archivos' => [
                'Almacenamiento compartido en red',
                'Recuperación de archivos desde respaldo',
                'Solicitud de espacio adicional de almacenamiento',
            ],
            'Servicios de Aplicaciones Institucionales' => [
                'Acceso al SIGA',
                'Acceso al SIAF',
                'Acceso al sistema de Trámite Documentario',
                'Acceso al sistema de Planillas / RRHH',
                'Acceso al sistema de Recaudación Tributaria',
            ],
            'Servicios de Digitalización y Documento Electrónico' => [
                'Digitalización de documentos físicos',
                'Obtención de firma digital institucional',
                'Soporte al expediente electrónico',
            ],
            'Servicios de Capacitación TI' => [
                'Capacitación en ofimática',
                'Capacitación en aplicativos institucionales',
                'Inducción TI para personal nuevo',
            ],
        ];

        foreach ($catalogo as $categoria => $servicios) {
            DB::table('categorias')->insertOrIgnore(['nombre' => $categoria, 'activo' => true]);

            $categoriaId = DB::table('categorias')->where('nombre', $categoria)->value('id');

            foreach ($servicios as $servicio) {
                DB::table('servicios')->updateOrInsert(
                    ['categoria_id' => $categoriaId, 'nombre' => $servicio],
                    ['categoria_id' => $categoriaId, 'tipo_id' => $tipoSolicitudId, 'nombre' => $servicio, 'activo' => true]
                );
            }
        }
    }
}
