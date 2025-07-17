<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla de productos
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del producto
            $table->decimal('price', 10, 2); // Precio con 2 decimales
            $table->integer('stock'); // Cantidad en stock
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Relación con categoría
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};