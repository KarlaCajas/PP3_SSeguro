<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder para crear un usuario con rol de pagos
 */
class PaymentUserSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Buscar o crear el rol de pagos
        $pagoRole = Role::firstOrCreate(['name' => 'pagos']);

        // Crear usuario con rol de pagos
        $paymentUser = User::firstOrCreate(
            ['email' => 'pagos@barespe.com'],
            [
                'name' => 'Validador de Pagos',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        // Asignar rol de pagos si no lo tiene
        if (!$paymentUser->hasRole('pagos')) {
            $paymentUser->assignRole('pagos');
        }

        // Crear usuario cliente para pruebas
        $clienteUser = User::firstOrCreate(
            ['email' => 'cliente@barespe.com'],
            [
                'name' => 'Cliente Test',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        // Asignar rol de cliente si no lo tiene
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);
        if (!$clienteUser->hasRole('cliente')) {
            $clienteUser->assignRole('cliente');
        }
    }
}
