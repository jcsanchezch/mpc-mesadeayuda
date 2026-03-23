<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            DependenciasSeeder::class,
            TrabajadoresSeeder::class,
            ServiciosSeeder::class,
            EstadosSeeder::class,
        ]);
    }
}
