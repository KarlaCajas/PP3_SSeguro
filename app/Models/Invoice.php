<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para las facturas
 */
class Invoice extends Model
{
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'subtotal',
        'tax',
        'total',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    /**
     * Conversión de tipos para los campos
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    /**
     * Generar número de factura único
     */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', now())->count() + 1;
        return "FAC-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relación: La factura pertenece a un usuario (vendedor)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: La factura pertenece a un cliente (opcional)
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relación: Usuario que canceló la factura
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Relación: La factura tiene muchos items
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope: Facturas activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Facturas canceladas
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope: Facturas del día actual
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Cancelar factura y reversar stock
     */
    public function cancel(User $cancelledBy, string $reason): bool
    {
        return DB::transaction(function () use ($cancelledBy, $reason) {
            // Reversar stock de todos los productos
            foreach ($this->items as $item) {
                $product = $item->product;
                $product->increment('stock', $item->quantity);
            }

            // Marcar factura como cancelada
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy->id,
                'cancellation_reason' => $reason,
            ]);

            return true;
        });
    }

    /**
     * Verificar si puede ser cancelada por el usuario
     */
    public function canBeCancelledBy(User $user): bool
    {
        // Solo puede cancelar el usuario que la creó o un administrador
        return $this->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Verificar si está activa
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si está cancelada
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Recalcular totales de la factura basado en los items
     */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $tax = $this->items->sum('tax_amount');
        $total = $this->items->sum('total_price');

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    /**
     * Obtener el subtotal calculado
     */
    public function getCalculatedSubtotal(): float
    {
        return $this->items->sum('subtotal');
    }

    /**
     * Obtener el IVA calculado
     */
    public function getCalculatedTax(): float
    {
        return $this->items->sum('tax_amount');
    }

    /**
     * Obtener el total calculado
     */
    public function getCalculatedTotal(): float
    {
        return $this->items->sum('total_price');
    }
}
