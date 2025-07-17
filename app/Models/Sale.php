<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para las ventas realizadas
 */
class Sale extends Model
{
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
    ];

    /**
     * Conversión de tipos para los campos
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: La venta pertenece a un usuario (cajera)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: La venta pertenece a un producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: Ventas del día actual
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: Ventas de un usuario específico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessor: Verificar si la venta es del día actual
     */
    public function getIsEditableAttribute(): bool
    {
        return $this->created_at->isToday();
    }

    /**
     * Accessor: Obtener el nombre del cajero
     */
    public function getCashierNameAttribute(): string
    {
        return $this->user->name;
    }
}