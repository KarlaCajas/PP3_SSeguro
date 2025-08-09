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
        // Primero, actualizar las facturas existentes que tengan status 'cancelled' sin razón válida
        DB::table('invoices')
            ->where('status', 'cancelled')
            ->whereNull('cancellation_reason')
            ->update(['status' => 'pending']);

        // Luego, modificar la tabla para establecer 'pending' como valor por defecto
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending', 'paid', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending', 'paid', 'cancelled'])->default('active')->change();
        });
    }
};
