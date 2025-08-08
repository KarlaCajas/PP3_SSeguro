<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Seeder para crear los roles del sistema
 */
class RoleSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Crear roles del sistema
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'secre']);
        Role::create(['name' => 'bodega']);
        Role::create(['name' => 'ventas']);
        Role::create(['name' => 'cliente']);
        Role::create(['name' => 'pagos']);
    }
}