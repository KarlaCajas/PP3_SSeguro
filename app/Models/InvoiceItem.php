<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para los items de factura
 */
class InvoiceItem extends Model
{
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'total_price',
    ];

    /**
     * Conversión de tipos para los campos
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relación: El item pertenece a una factura
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relación: El item pertenece a un producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcular el subtotal (sin IVA)
     */
    public function calculateSubtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Calcular el monto del IVA
     */
    public function calculateTaxAmount(): float
    {
        return $this->calculateSubtotal() * ($this->tax_rate / 100);
    }

    /**
     * Calcular el total con IVA
     */
    public function calculateTotalWithTax(): float
    {
        return $this->calculateSubtotal() + $this->calculateTaxAmount();
    }

    /**
     * Establecer automáticamente los cálculos al guardar
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Solo calcular si no están ya establecidos
            if (is_null($item->subtotal)) {
                $item->subtotal = $item->calculateSubtotal();
            }
            if (is_null($item->tax_amount)) {
                $item->tax_amount = $item->calculateTaxAmount();
            }
            if (is_null($item->total_price)) {
                $item->total_price = $item->calculateTotalWithTax();
            }
        });
    }
}
