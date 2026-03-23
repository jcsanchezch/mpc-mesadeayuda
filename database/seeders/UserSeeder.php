<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::firstOrCreate(
            ['usuario' => 'CSANCHEZ'],
            [
                'dni'     => '00000000',
                'paterno' => 'SANCHEZ',
                'materno' => 'CHUNQUE',
                'nombres' => 'JUAN CARLOS',
                'activo'  => true,
            ]
        );

        $user->syncRoles('ADMINISTRADOR');
    }
}
