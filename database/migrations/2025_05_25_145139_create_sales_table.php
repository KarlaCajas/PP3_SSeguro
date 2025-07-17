<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla de ventas
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que realizó la venta
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Producto vendido
            $table->integer('quantity'); // Cantidad vendida
            $table->decimal('unit_price', 10, 2); // Precio unitario al momento de la venta
            $table->decimal('total_price', 10, 2); // Precio total (quantity * unit_price)
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};