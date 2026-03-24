<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrabajadoresSeeder extends Seeder
{
    public function run(): void
    {
        $depId   = fn(string $abrev)  => DB::table('dependencias')->where('abreviatura', $abrev)->value('id');
        $localId = fn(string $nombre) => DB::table('locales')->where('nombre', $nombre)->value('id');

        // ── TRABAJADORES ──────────────────────────────────────────────────────
        $trabajadores = [
            // ── QHAPAC ÑAN (principal) — 5 trabajadores ──────────────
            // CSANCHEZ — vinculado al usuario existente
            [
                'dni'           => '00000000',
                'paterno'       => 'SANCHEZ',
                'materno'       => 'CHUNQUE',
                'nombres'       => 'JUAN CARLOS',
                'dependencia'   => 'OTI',
                'local'         => 'QHAPAC ÑAN',
                'activo'        => true,
                'vincular_user' => 'CSANCHEZ',
            ],
            [
                'dni'         => '45123678',
                'paterno'     => 'RAMIREZ',
                'materno'     => 'FLORES',
                'nombres'     => 'JORGE LUIS',
                'dependencia' => 'OTI',
                'local'       => 'QHAPAC ÑAN',
                'activo'      => true,
            ],
            [
                'dni'         => '72345891',
                'paterno'     => 'TORRES',
                'materno'     => 'MENDEZ',
                'nombres'     => 'ANA MARIA',
                'dependencia' => 'OGAF',
                'local'       => 'QHAPAC ÑAN',
                'activo'      => true,
            ],
            [
                'dni'         => '61234507',
                'paterno'     => 'GUTIERREZ',
                'materno'     => 'ROJAS',
                'nombres'     => 'PEDRO ANTONIO',
                'dependencia' => 'GM',
                'local'       => 'QHAPAC ÑAN',
                'activo'      => true,
            ],
            [
                'dni'         => '48901234',
                'paterno'     => 'VARGAS',
                'materno'     => 'CASTILLO',
                'nombres'     => 'LUCIA BEATRIZ',
                'dependencia' => 'OGAJ',
                'local'       => 'QHAPAC ÑAN',
                'activo'      => true,
            ],
            // ── CASA MIGUEL ESPINACH — 2 trabajadores ─────────────
            [
                'dni'         => '53782019',
                'paterno'     => 'DIAZ',
                'materno'     => 'PAREDES',
                'nombres'     => 'ROBERTO CARLOS',
                'dependencia' => 'OC',
                'local'       => 'CASA MIGUEL ESPINACH',
                'activo'      => true,
            ],
            [
                'dni'         => '40128756',
                'paterno'     => 'MORALES',
                'materno'     => 'QUISPE',
                'nombres'     => 'ELENA ROSA',
                'dependencia' => 'OGPP',
                'local'       => 'CASA MIGUEL ESPINACH',
                'activo'      => true,
            ],
            // ── Resto sorteado entre otros locales ────────────────
            [
                'dni'         => '76543210',
                'paterno'     => 'HUANCA',
                'materno'     => 'MAMANI',
                'nombres'     => 'FELIX RAUL',
                'dependencia' => 'GIP',
                'local'       => 'ESTADIO MUNICIPAL',
                'activo'      => true,
            ],
            [
                'dni'         => '68901345',
                'paterno'     => 'LEON',
                'materno'     => 'VEGA',
                'nombres'     => 'CARMEN SOFIA',
                'dependencia' => 'ORCP',
                'local'       => 'SERENAZGO',
                'activo'      => true,
            ],
            [
                'dni'         => '59234781',
                'paterno'     => 'RAMOS',
                'materno'     => 'SILVA',
                'nombres'     => 'MIGUEL ANGEL',
                'dependencia' => 'OMAC',
                'local'       => 'CENTRO MEDICO MUNICIPAL',
                'activo'      => true,
            ],
            [
                'dni'         => '47890123',
                'paterno'     => 'CHÁVEZ',
                'materno'     => 'LUNA',
                'nombres'     => 'PATRICIA ISABEL',
                'dependencia' => 'OPM',
                'local'       => 'REAL PLAZA',
                'activo'      => true,
            ],
            [
                'dni'         => '80123456',
                'paterno'     => 'CONDORI',
                'materno'     => 'APAZA',
                'nombres'     => 'JUAN PABLO',
                'dependencia' => 'OTI',
                'local'       => 'POLICIA MUNICIPAL',
                'activo'      => true,
            ],
            [
                'dni'         => '81234567',
                'paterno'     => 'QUISPE',
                'materno'     => 'CCAMA',
                'nombres'     => 'MARIA ELENA',
                'dependencia' => 'OGAF',
                'local'       => 'MERCADO CENTRAL DE CAJAMARCA',
                'activo'      => true,
            ],
            [
                'dni'         => '82345678',
                'paterno'     => 'MAMANI',
                'materno'     => 'HUANCA',
                'nombres'     => 'CARLOS DAVID',
                'dependencia' => 'GM',
                'local'       => 'CUNA JARDIN',
                'activo'      => true,
            ],
        ];

        foreach ($trabajadores as $data) {
            $id = DB::table('trabajadores')->updateOrInsert(
                ['dni' => $data['dni']],
                [
                    'dependencia_id' => $depId($data['dependencia']),
                    'local_id'       => isset($data['local']) ? $localId($data['local']) : null,
                    'dni'            => $data['dni'],
                    'paterno'        => $data['paterno'],
                    'materno'        => $data['materno'],
                    'nombres'        => $data['nombres'],
                    'celular'        => $data['celular'] ?? null,
                    'activo'         => $data['activo'],
                ]
            );

            // Vincular con usuario existente si corresponde
            if (!empty($data['vincular_user'])) {
                $trabajadorId = DB::table('trabajadores')->where('dni', $data['dni'])->value('id');
                DB::table('users')
                    ->where('usuario', $data['vincular_user'])
                    ->update(['trabajador_id' => $trabajadorId]);
            }
        }

        // ── ESPECIALISTAS ─────────────────────────────────────────────────────
        // 4 especialistas: 2 regulares + 2 voluntarios
        $especialistas = [
            ['dni' => '45123678', 'vinculo_laboral' => true],  // RAMIREZ
            ['dni' => '53782019', 'vinculo_laboral' => true],  // DIAZ
            ['dni' => '80123456', 'vinculo_laboral' => false], // CONDORI
            ['dni' => '81234567', 'vinculo_laboral' => false], // QUISPE
        ];

        foreach ($especialistas as $esp) {
            $trabajadorId = DB::table('trabajadores')->where('dni', $esp['dni'])->value('id');
            if ($trabajadorId) {
                DB::table('especialistas')->updateOrInsert(
                    ['trabajador_id' => $trabajadorId],
                    [
                        'trabajador_id'   => $trabajadorId,
                        'vinculo_laboral' => $esp['vinculo_laboral'],
                        'activo'          => true,
                    ]
                );
            }
        }
    }
}
