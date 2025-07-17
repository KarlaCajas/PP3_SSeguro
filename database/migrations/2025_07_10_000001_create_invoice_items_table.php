<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla de items de factura
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade'); // Factura a la que pertenece
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Producto
            $table->string('product_name'); // Nombre del producto al momento de la venta
            $table->integer('quantity'); // Cantidad
            $table->decimal('unit_price', 10, 2); // Precio unitario
            $table->decimal('total_price', 10, 2); // Precio total (quantity * unit_price)
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
