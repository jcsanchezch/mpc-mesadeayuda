<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin = User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@mesadeayuda.test'),
        ], [
            'name' => env('ADMIN_NAME', 'Administrador'),
            'password' => env('ADMIN_PASSWORD', 'password'),
        ]);

        $admin->syncRoles(['ADMIN']);
    }
}
