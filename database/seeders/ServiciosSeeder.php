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
                ['nombre' => 'Provisión de equipo de cómputo',           'descripcion' => 'Asignación de computadora de escritorio o laptop para el personal según requerimiento del área.'],
                ['nombre' => 'Instalación o actualización de software',  'descripcion' => 'Instalación, actualización o desinstalación de programas y aplicaciones en equipos institucionales.'],
                ['nombre' => 'Reparación o reemplazo de equipo',         'descripcion' => 'Diagnóstico, reparación o sustitución de equipos de cómputo con fallas de hardware.'],
                ['nombre' => 'Configuración de impresora o escáner',     'descripcion' => 'Instalación y configuración de impresoras, escáneres y equipos multifunción en la estación de trabajo.'],
            ],
            'Servicios de Comunicación' => [
                ['nombre' => 'Creación de cuenta de correo institucional',        'descripcion' => 'Creación de una cuenta de correo electrónico corporativo para personal de la institución.'],
                ['nombre' => 'Acceso a plataforma de videoconferencia',           'descripcion' => 'Habilitación de acceso a herramientas de videoconferencia institucionales como Zoom, Teams u otras.'],
                ['nombre' => 'Acceso a directorio telefónico interno (VoIP)',      'descripcion' => 'Configuración y acceso al sistema de telefonía IP para comunicación interna entre áreas.'],
                ['nombre' => 'Acceso a la intranet municipal',                    'descripcion' => 'Habilitación del acceso al portal de intranet con recursos, noticias y herramientas internas de la municipalidad.'],
            ],
            'Servicios de Conectividad' => [
                ['nombre' => 'Acceso a internet',                        'descripcion' => 'Habilitación o restablecimiento del acceso a internet desde la estación de trabajo del usuario.'],
                ['nombre' => 'Acceso a la red institucional (LAN/WiFi)', 'descripcion' => 'Configuración y acceso a la red local cableada o inalámbrica de la institución.'],
                ['nombre' => 'Acceso remoto a la red (VPN)',             'descripcion' => 'Configuración de VPN para permitir el acceso seguro a los recursos institucionales desde ubicaciones externas.'],
            ],
            'Servicios de Acceso e Identidad' => [
                ['nombre' => 'Creación de cuenta de usuario',                  'descripcion' => 'Creación de credenciales de acceso a los sistemas institucionales para personal nuevo o reingresante.'],
                ['nombre' => 'Restablecimiento de contraseña',                 'descripcion' => 'Recuperación o reseteo de contraseña para acceso a sistemas o correo institucional.'],
                ['nombre' => 'Solicitud de acceso a sistema o aplicativo',     'descripcion' => 'Habilitación de permisos para acceder a un sistema o módulo específico según el rol del usuario.'],
                ['nombre' => 'Modificación o baja de cuenta de usuario',       'descripcion' => 'Actualización de datos, cambio de permisos o desactivación de cuentas de usuarios que cambian de función o se retiran.'],
            ],
            'Servicios de Almacenamiento y Archivos' => [
                ['nombre' => 'Almacenamiento compartido en red',                     'descripcion' => 'Asignación de carpetas compartidas en el servidor de archivos para trabajo colaborativo entre áreas.'],
                ['nombre' => 'Recuperación de archivos desde respaldo',              'descripcion' => 'Restauración de archivos o carpetas eliminadas o dañadas a partir de copias de seguridad disponibles.'],
                ['nombre' => 'Solicitud de espacio adicional de almacenamiento',     'descripcion' => 'Ampliación de la cuota de almacenamiento asignada al usuario o área solicitante.'],
            ],
            'Servicios de Aplicaciones Institucionales' => [
                ['nombre' => 'Acceso al SIGA',                               'descripcion' => 'Habilitación de usuario y permisos en el Sistema Integrado de Gestión Administrativa (SIGA) del MEF.'],
                ['nombre' => 'Acceso al SIAF',                               'descripcion' => 'Habilitación de usuario y permisos en el Sistema Integrado de Administración Financiera (SIAF) del MEF.'],
                ['nombre' => 'Acceso al sistema de Trámite Documentario',    'descripcion' => 'Creación o habilitación de cuenta en el sistema de gestión de trámite documentario institucional.'],
                ['nombre' => 'Acceso al sistema de Planillas / RRHH',        'descripcion' => 'Habilitación de acceso al sistema de gestión de recursos humanos y planillas del personal.'],
                ['nombre' => 'Acceso al sistema de Recaudación Tributaria',  'descripcion' => 'Creación o habilitación de cuenta en el sistema de recaudación y administración tributaria municipal.'],
            ],
            'Servicios de Digitalización y Documento Electrónico' => [
                ['nombre' => 'Digitalización de documentos físicos',      'descripcion' => 'Escaneo y conversión de documentos físicos a formato digital para su archivo o gestión electrónica.'],
                ['nombre' => 'Obtención de firma digital institucional',  'descripcion' => 'Gestión y emisión de certificado de firma digital para el personal autorizado a firmar documentos electrónicos.'],
                ['nombre' => 'Soporte al expediente electrónico',         'descripcion' => 'Asistencia en el uso, generación y seguimiento de expedientes electrónicos dentro de la plataforma institucional.'],
            ],
            'Servicios de Capacitación TI' => [
                ['nombre' => 'Capacitación en ofimática',                   'descripcion' => 'Formación en el uso de herramientas de oficina como Word, Excel, PowerPoint y similares.'],
                ['nombre' => 'Capacitación en aplicativos institucionales', 'descripcion' => 'Entrenamiento en el manejo de los sistemas y aplicativos propios de la municipalidad.'],
                ['nombre' => 'Inducción TI para personal nuevo',            'descripcion' => 'Orientación inicial sobre los recursos tecnológicos, políticas de uso y sistemas disponibles para el personal que ingresa.'],
            ],
        ];

        foreach ($catalogo as $categoria => $servicios) {
            DB::table('categorias')->insertOrIgnore(['nombre' => $categoria, 'activo' => true]);

            $categoriaId = DB::table('categorias')->where('nombre', $categoria)->value('id');

            foreach ($servicios as $servicio) {
                DB::table('servicios')->updateOrInsert(
                    ['categoria_id' => $categoriaId, 'nombre' => $servicio['nombre']],
                    ['categoria_id' => $categoriaId, 'tipo_id' => $tipoSolicitudId, 'nombre' => $servicio['nombre'], 'descripcion' => $servicio['descripcion'], 'activo' => true]
                );
            }
        }
    }
}
