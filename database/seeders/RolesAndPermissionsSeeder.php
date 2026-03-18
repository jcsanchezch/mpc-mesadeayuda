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
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'mesa_servicio',
            'atencion',
            'reportes',
            'usuarios',
            'profesionales',
            'voluntarios',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'ADMIN' => [
                'mesa_servicio',
                'atencion',
                'reportes',
                'usuarios',
                'profesionales',
                'voluntarios',
            ],
            'COORDINADOR' => [
                'mesa_servicio',
                'atencion',
                'reportes',
                'voluntarios',
            ],
            'MESA_SERVICIO' => [
                'mesa_servicio',
                'atencion',
                'reportes',
            ],
            'PROFESIONAL' => [
                'atencion',
            ],
            'VOLUNTARIOS' => [
                'atencion',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
