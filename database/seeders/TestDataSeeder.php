<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder para crear datos básicos del sistema
 */
class TestDataSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run(): void
    {
        // Crear solo el usuario admin
        $this->createAdminUser();
        
        // Crear categorías de prueba
        $this->createCategories();
        
        // Crear productos de prueba
        $this->createProducts();
    }
    
    /**
     * Crear usuario administrador
     */
    private function createAdminUser(): void
    {
        $admin = User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@barespe.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');
        
        echo "✅ Usuario admin creado: admin@barespe.com / password123\n";
        
        // Crear usuarios de ejemplo para cada rol
        $this->createSampleUsers();
    }
    
    /**
     * Crear usuarios de ejemplo para cada rol
     */
    private function createSampleUsers(): void
    {
        // Usuario Secretaria
        $secretaria = User::create([
            'name' => 'María González',
            'email' => 'secretaria@barespe.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $secretaria->assignRole('secre');
        
        // Usuario Bodega
        $bodega = User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'bodega@barespe.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $bodega->assignRole('bodega');
        
        // Usuario Ventas
        $ventas = User::create([
            'name' => 'Ana López',
            'email' => 'ventas@barespe.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $ventas->assignRole('ventas');
        
        // Usuario Cliente
        $cliente = User::create([
            'name' => 'Juan Pérez',
            'email' => 'cliente@barespe.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $cliente->assignRole('cliente');
        
        echo "✅ Usuarios de ejemplo creados para todos los roles\n";
    }
    
    /**
     * Crear categorías de prueba
     */
    private function createCategories(): void
    {
        $categories = [
            'Bebidas',
            'Comidas Rápidas',
            'Postres',
            'Snacks',
            'Bebidas Alcohólicas',
        ];
        
        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }
        
        echo "✅ Categorías creadas: " . implode(', ', $categories) . "\n";
    }
    
    /**
     * Crear productos de prueba
     */
    private function createProducts(): void
    {
        $products = [
            // Bebidas
            ['name' => 'Coca Cola 350ml', 'price' => 2.50, 'stock' => 50, 'category' => 'Bebidas'],
            ['name' => 'Pepsi 350ml', 'price' => 2.30, 'stock' => 45, 'category' => 'Bebidas'],
            ['name' => 'Agua Mineral 500ml', 'price' => 1.00, 'stock' => 100, 'category' => 'Bebidas'],
            ['name' => 'Jugo de Naranja', 'price' => 3.00, 'stock' => 30, 'category' => 'Bebidas'],
            
            // Comidas Rápidas
            ['name' => 'Hamburguesa Clásica', 'price' => 8.50, 'stock' => 25, 'category' => 'Comidas Rápidas'],
            ['name' => 'Hamburguesa Especial', 'price' => 12.00, 'stock' => 20, 'category' => 'Comidas Rápidas'],
            ['name' => 'Hot Dog', 'price' => 5.00, 'stock' => 30, 'category' => 'Comidas Rápidas'],
            ['name' => 'Papas Fritas', 'price' => 4.50, 'stock' => 40, 'category' => 'Comidas Rápidas'],
            
            // Postres
            ['name' => 'Helado de Vainilla', 'price' => 3.50, 'stock' => 15, 'category' => 'Postres'],
            ['name' => 'Brownie', 'price' => 4.00, 'stock' => 20, 'category' => 'Postres'],
            ['name' => 'Cheesecake', 'price' => 6.00, 'stock' => 10, 'category' => 'Postres'],
            
            // Snacks
            ['name' => 'Doritos', 'price' => 2.00, 'stock' => 60, 'category' => 'Snacks'],
            ['name' => 'Pringles', 'price' => 3.50, 'stock' => 35, 'category' => 'Snacks'],
            ['name' => 'Chocolate Snickers', 'price' => 1.50, 'stock' => 80, 'category' => 'Snacks'],
            
            // Bebidas Alcohólicas
            ['name' => 'Cerveza Pilsener', 'price' => 2.80, 'stock' => 0, 'category' => 'Bebidas Alcohólicas'], // Sin stock para pruebas
            ['name' => 'Vino Tinto', 'price' => 15.00, 'stock' => 5, 'category' => 'Bebidas Alcohólicas'], // Stock bajo
        ];
        
        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::create([
                'name' => $productData['name'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'category_id' => $category->id,
            ]);
        }
        
        echo "✅ " . count($products) . " productos creados\n";
    }
}