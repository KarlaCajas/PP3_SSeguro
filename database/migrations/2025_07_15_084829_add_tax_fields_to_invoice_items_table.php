<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price'); // Tasa de IVA (15.00 o 0.00)
            $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate'); // Monto del IVA
            $table->decimal('subtotal', 10, 2)->nullable()->after('tax_amount'); // Subtotal sin IVA (quantity * unit_price)
        });

        // Actualizar registros existentes
        DB::statement('UPDATE invoice_items SET subtotal = quantity * unit_price WHERE subtotal IS NULL');
        
        // Hacer la columna NOT NULL después de llenar los datos
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'tax_amount', 'subtotal']);
        });
    }
};
