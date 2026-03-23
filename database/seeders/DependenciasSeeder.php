<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DependenciasSeeder extends Seeder
{
    public function run(): void
    {
        $dependencias = [
            ['nombre' => 'Alcaldía', 'abreviatura' => 'A', 'activo' => true, 'origen_id' => 2],
            ['nombre' => 'Concejo Municipal', 'abreviatura' => 'CM', 'activo' => true, 'origen_id' => 104],
            ['nombre' => 'Gerencia de Desarrollo Ambiental', 'abreviatura' => 'GDA', 'activo' => true, 'origen_id' => 57],
            ['nombre' => 'Gerencia de Desarrollo Económico', 'abreviatura' => 'GDE', 'activo' => true, 'origen_id' => 41],
            ['nombre' => 'Gerencia de Desarrollo Social y Humano', 'abreviatura' => 'GDSH', 'activo' => true, 'origen_id' => 136],
            ['nombre' => 'Gerencia de Desarrollo Territorial y Urbano', 'abreviatura' => 'GDTU', 'activo' => true, 'origen_id' => 125],
            ['nombre' => 'Gerencia de Infraestructura Pública', 'abreviatura' => 'GIP', 'activo' => true, 'origen_id' => 121],
            ['nombre' => 'Gerencia de Seguridad Ciudadana', 'abreviatura' => 'GSC', 'activo' => true, 'origen_id' => 61],
            ['nombre' => 'Gerencia de Transportes y Seguridad Vial', 'abreviatura' => 'GTSV', 'activo' => true, 'origen_id' => 128],
            ['nombre' => 'Gerencia de Turismo y Cultura', 'abreviatura' => 'GTC', 'activo' => true, 'origen_id' => 142],
            ['nombre' => 'Gerencia Municipal', 'abreviatura' => 'GM', 'activo' => true, 'origen_id' => 9],
            ['nombre' => 'Oficina de Abastecimiento y Control Patrimonial', 'abreviatura' => 'OACP', 'activo' => true, 'origen_id' => 114],
            ['nombre' => 'Oficina de Contabilidad', 'abreviatura' => 'OC', 'activo' => true, 'origen_id' => 115],
            ['nombre' => 'Oficina de Mejor Atención al Ciudadano', 'abreviatura' => 'OMAC', 'activo' => true, 'origen_id' => 110],
            ['nombre' => 'Oficina de Planeamiento y Modernización', 'abreviatura' => 'OPM', 'activo' => true, 'origen_id' => 118],
            ['nombre' => 'Oficina de Presupuesto', 'abreviatura' => 'OP', 'activo' => true, 'origen_id' => 119],
            ['nombre' => 'Oficina de Programación Multianual de Inversiones', 'abreviatura' => 'OPMI', 'activo' => true, 'origen_id' => 120],
            ['nombre' => 'Oficina de Remuneraciones y Control de Personal', 'abreviatura' => 'ORCP', 'activo' => true, 'origen_id' => 112],
            ['nombre' => 'Oficina de Tecnologías de la Información', 'abreviatura' => 'OTI', 'activo' => true, 'origen_id' => 117],
            ['nombre' => 'Oficina de Tesorería', 'abreviatura' => 'OT', 'activo' => true, 'origen_id' => 116],
            ['nombre' => 'Oficina General de Administración y Finanzas', 'abreviatura' => 'OGAF', 'activo' => true, 'origen_id' => 113],
            ['nombre' => 'Oficina General de Asesoría Jurídica', 'abreviatura' => 'OGAJ', 'activo' => true, 'origen_id' => 11],
            ['nombre' => 'Oficina General de Gestión de Recursos Humanos', 'abreviatura' => 'OGGRRHH', 'activo' => true, 'origen_id' => 24],
            ['nombre' => 'Oficina General de Gestión Documentaria y Atención al Ciudadano', 'abreviatura' => 'OGGDAC', 'activo' => true, 'origen_id' => 109],
            ['nombre' => 'Oficina General de Imagen y Comunicaciones Institucionales', 'abreviatura' => 'OGICI', 'activo' => true, 'origen_id' => 111],
            ['nombre' => 'Oficina General de Planeamiento y Presupuesto', 'abreviatura' => 'OGPP', 'activo' => true, 'origen_id' => 12],
            ['nombre' => 'Procuraduría Pública', 'abreviatura' => 'PP', 'activo' => true, 'origen_id' => 106],
            ['nombre' => 'Subgerencia de Comercialización y Licencias', 'abreviatura' => 'SCL', 'activo' => true, 'origen_id' => 132],
            ['nombre' => 'Subgerencia de Educación Recreación y Deporte', 'abreviatura' => 'SERD', 'activo' => true, 'origen_id' => 139],
            ['nombre' => 'Subgerencia de Ejecución de Inversiones', 'abreviatura' => 'SEI', 'activo' => true, 'origen_id' => 123],
            ['nombre' => 'Subgerencia de Formulación de Inversiones', 'abreviatura' => 'SFI', 'activo' => true, 'origen_id' => 122],
            ['nombre' => 'Subgerencia de Gestión Ambiental', 'abreviatura' => 'SGA', 'activo' => true, 'origen_id' => 133],
            ['nombre' => 'Subgerencia de Gestión del Riesgo de Desastres', 'abreviatura' => 'SGRD', 'activo' => true, 'origen_id' => 141],
            ['nombre' => 'Subgerencia de Gestión Integral de Residuos Sólidos', 'abreviatura' => 'SGIRS', 'activo' => true, 'origen_id' => 134],
            ['nombre' => 'Subgerencia de Inspección y Seguridad Vial', 'abreviatura' => 'SISV', 'activo' => true, 'origen_id' => 130],
            ['nombre' => 'Subgerencia de Licencias y Permisos Urbanos', 'abreviatura' => 'SLPU', 'activo' => true, 'origen_id' => 127],
            ['nombre' => 'Subgerencia de Mantenimiento y Gestión de Caminos', 'abreviatura' => 'SMGC', 'activo' => true, 'origen_id' => 124],
            ['nombre' => 'Subgerencia de Participación Vecinal y Registro Civil', 'abreviatura' => 'SPVRC', 'activo' => true, 'origen_id' => 138],
            ['nombre' => 'Subgerencia de Planificación Territorial y Centro Histórico', 'abreviatura' => 'SPTCH', 'activo' => true, 'origen_id' => 126],
            ['nombre' => 'Subgerencia de Productividad y Promoción de la Inversión', 'abreviatura' => 'SPPI', 'activo' => true, 'origen_id' => 131],
            ['nombre' => 'Subgerencia de Programas Sociales y Empadronamiento', 'abreviatura' => 'SPSE', 'activo' => true, 'origen_id' => 137],
            ['nombre' => 'Subgerencia de Regulación y Autorizaciones de Transporte', 'abreviatura' => 'SRAT', 'activo' => true, 'origen_id' => 129],
            ['nombre' => 'Subgerencia de Salud', 'abreviatura' => 'SSA', 'activo' => true, 'origen_id' => 54],
            ['nombre' => 'Subgerencia de Saneamiento Básico', 'abreviatura' => 'SSB', 'activo' => true, 'origen_id' => 135],
            ['nombre' => 'Subgerencia de Serenazgo', 'abreviatura' => 'SSE', 'activo' => true, 'origen_id' => 140],
        ];

        foreach ($dependencias as $dependencia) {
            DB::table('dependencias')->updateOrInsert(
                ['origen_id' => $dependencia['origen_id']],
                $dependencia
            );
        }
    }
}
