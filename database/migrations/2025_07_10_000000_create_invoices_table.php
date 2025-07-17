<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración para crear la tabla de facturas
 */
return new class extends Migration
{
    /**
     * Ejecutar las migraciones
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Número de factura
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que creó la factura
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null'); // Cliente (opcional)
            $table->string('customer_name'); // Nombre del cliente
            $table->string('customer_email')->nullable(); // Email del cliente
            $table->decimal('subtotal', 10, 2); // Subtotal
            $table->decimal('tax', 10, 2)->default(0); // Impuestos
            $table->decimal('total', 10, 2); // Total
            $table->enum('status', ['active', 'cancelled'])->default('active'); // Estado de la factura
            $table->timestamp('cancelled_at')->nullable(); // Fecha de cancelación
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null'); // Usuario que canceló
            $table->text('cancellation_reason')->nullable(); // Razón de cancelación
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
