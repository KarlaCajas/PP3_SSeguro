<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar el seeder de roles
        $this->call([
            RoleSeeder::class,
            TestDataSeeder::class, //seeder de datos de prueba
        ]);
    }
}