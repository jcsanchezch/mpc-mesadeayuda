<?php

namespace Database\Seeders;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Crear usuario administrador del sistema
        $admin = User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@mesadeayuda.test')],
            [
                'name'     => env('ADMIN_NAME', 'Administrador'),
                'password' => env('ADMIN_PASSWORD', 'password'),
            ]
        );

        $admin->syncRoles(['admin']);

        // Crear perfil de trabajador vinculado al usuario admin
        Trabajador::query()->updateOrCreate(
            ['user_id' => $admin->id],
            [
                'dni'      => env('ADMIN_DNI', '00000000'),
                'nombres'  => env('ADMIN_NAME', 'Administrador'),
                'apellidos' => 'Sistema',
                'activo'   => true,
            ]
        );
    }
}
