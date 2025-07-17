<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo para los productos del inventario
 */
class Product extends Model
{
    use SoftDeletes, LogsActivity;
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'name',
        'price',
        'stock',
        'category_id',
        'deletion_reason',
    ];

    /**
     * Conversión de tipos para los campos
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'category_id' => 'integer',
    ];

    /**
     * Relación: El producto pertenece a una categoría
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación: El producto puede tener muchas ventas
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relación: El producto puede tener muchos items de factura
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope: Productos con stock disponible
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope: Productos con stock bajo (<=10)
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '>', 0)->where('stock', '<=', 10);
    }

    /**
     * Scope: Productos sin stock
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    /**
     * Accessor: Estado del stock
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock == 0) {
            return 'Sin Stock';
        } elseif ($this->stock <= 10) {
            return 'Stock Bajo';
        } else {
            return 'Disponible';
        }
    }

    /**
     * Accessor: Color del estado del stock
     */
    public function getStockStatusColorAttribute(): string
    {
        if ($this->stock == 0) {
            return 'red';
        } elseif ($this->stock <= 10) {
            return 'yellow';
        } else {
            return 'green';
        }
    }

    /**
     * Relación: Usuario que eliminó este producto
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Verificar si el producto puede ser eliminado
     */
    public function canBeDeleted(): bool
    {
        return $this->sales()->count() === 0 && $this->invoiceItems()->count() === 0;
    }

    /**
     * Obtener el motivo por el cual no se puede eliminar
     */
    public function getDeletionBlockReason(): ?string
    {
        if ($this->sales()->count() > 0) {
            return 'El producto tiene ventas asociadas.';
        }

        if ($this->invoiceItems()->count() > 0) {
            return 'El producto está incluido en facturas.';
        }

        return null;
    }
}