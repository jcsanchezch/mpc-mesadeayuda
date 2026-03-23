<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            // Tickets - Mesa de Servicio
            'tickets.crear',
            'tickets.clasificar',
            'tickets.asignar',
            'tickets.reasignar',
            'tickets.solicitar_informacion',
            'tickets.cancelar',
            'tickets.actualizar',
            // Tickets - Especialista
            'tickets.registrar_inicio_atencion',
            'tickets.cerrar',
            'tickets.registrar_resolucion',
            // Reportes
            'reportes.reportes',
            // Administración
            'administracion.solicitantes',
            'administracion.voluntarios',
            'administracion.practicantes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $permisosMesaServicio = [
            'tickets.crear',
            'tickets.clasificar',
            'tickets.asignar',
            'tickets.reasignar',
            'tickets.solicitar_informacion',
            'tickets.cancelar',
            'tickets.actualizar',
        ];

        $permisosEspecialista = [
            'tickets.registrar_inicio_atencion',
            'tickets.solicitar_informacion',
            'tickets.cerrar',
            'tickets.clasificar',
            'tickets.registrar_resolucion',
        ];

        $permisosCoordinador = array_unique(array_merge(
            $permisosMesaServicio,
            $permisosEspecialista,
            ['reportes.reportes'],
        ));

        $permisosAdministrador = array_unique(array_merge(
            $permisosCoordinador,
            [
                'administracion.solicitantes',
                'administracion.voluntarios',
                'administracion.practicantes',
            ],
        ));

        Role::firstOrCreate(['name' => 'SOLICITANTE',    'guard_name' => 'web'])
            ->syncPermissions([]);

        Role::firstOrCreate(['name' => 'MESA_SERVICIO',  'guard_name' => 'web'])
            ->syncPermissions($permisosMesaServicio);

        Role::firstOrCreate(['name' => 'ESPECIALISTA',   'guard_name' => 'web'])
            ->syncPermissions($permisosEspecialista);

        Role::firstOrCreate(['name' => 'COORDINADOR',    'guard_name' => 'web'])
            ->syncPermissions($permisosCoordinador);

        Role::firstOrCreate(['name' => 'ADMINISTRADOR',  'guard_name' => 'web'])
            ->syncPermissions($permisosAdministrador);
    }
}
