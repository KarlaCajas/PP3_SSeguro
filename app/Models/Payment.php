<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

/**
 * Modelo para los pagos de facturas
 */
class Payment extends Model
{
    use LogsActivity;

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'invoice_id',
        'tipo_pago',
        'monto',
        'numero_transaccion',
        'observacion',
        'estado',
        'pagado_por',
        'validado_por',
        'validated_at',
    ];

    /**
     * Conversión de tipos para los campos
     */
    protected $casts = [
        'monto' => 'decimal:2',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Valores válidos para tipo_pago
     */
    public const TIPOS_PAGO = [
        'efectivo' => 'Efectivo',
        'tarjeta' => 'Tarjeta',
        'transferencia' => 'Transferencia',
        'cheque' => 'Cheque',
    ];

    /**
     * Valores válidos para estado
     */
    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ];

    /**
     * Relación: El pago pertenece a una factura
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relación: El pago fue realizado por un usuario (cliente)
     */
    public function pagadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    /**
     * Relación: El pago fue validado por un usuario
     */
    public function validadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    /**
     * Scope: Pagos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope: Pagos aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    /**
     * Scope: Pagos rechazados
     */
    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }

    /**
     * Verificar si el pago está pendiente
     */
    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Verificar si el pago está aprobado
     */
    public function isAprobado(): bool
    {
        return $this->estado === 'aprobado';
    }

    /**
     * Verificar si el pago está rechazado
     */
    public function isRechazado(): bool
    {
        return $this->estado === 'rechazado';
    }

    /**
     * Aprobar el pago
     */
    public function aprobar(User $validador): bool
    {
        $this->update([
            'estado' => 'aprobado',
            'validado_por' => $validador->id,
            'validated_at' => now(),
        ]);

        // Actualizar estado de la factura a pagada
        $this->invoice()->update(['status' => 'paid']);

        return true;
    }

    /**
     * Rechazar el pago
     */
    public function rechazar(User $validador): bool
    {
        $this->update([
            'estado' => 'rechazado',
            'validado_por' => $validador->id,
            'validated_at' => now(),
        ]);

        // La factura se mantiene pendiente
        return true;
    }

    /**
     * Obtener el nombre del tipo de pago
     */
    public function getTipoPagoNombre(): string
    {
        return self::TIPOS_PAGO[$this->tipo_pago] ?? $this->tipo_pago;
    }

    /**
     * Obtener el nombre del estado
     */
    public function getEstadoNombre(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}
