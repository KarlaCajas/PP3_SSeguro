<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * ADVERTENCIA: Este modelo almacena tokens en texto plano
 * Esto es INSEGURO y solo debe usarse para desarrollo/educación
 */
class PlainTextToken extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generar un nuevo token
     */
    public static function generateToken(): string
    {
        return Str::random(40); // Token de 40 caracteres
    }

    /**
     * Verificar si el token ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Marcar el token como usado
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Verificar si el token tiene una habilidad específica
     */
    public function can(string $ability): bool
    {
        if (empty($this->abilities)) {
            return true; // Sin restricciones
        }

        return in_array($ability, $this->abilities) || in_array('*', $this->abilities);
    }
}
