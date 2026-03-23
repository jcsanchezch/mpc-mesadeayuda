<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            InfraestructuraSeeder::class,
            TrabajadoresSeeder::class,
            ServiciosSeeder::class,
            ParametrosSeeder::class,
        ]);
    }
}
