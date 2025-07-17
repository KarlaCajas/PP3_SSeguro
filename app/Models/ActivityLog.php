<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'old_values',
        'new_values',
        'reason',
        'ip_address',
        'user_agent',
    ];

    /**
     * Conversión de tipos para los campos
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: El log pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica: El modelo relacionado
     */
    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Scope: Logs de un tipo de modelo específico
     */
    public function scopeForModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope: Logs de una acción específica
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Logs de un usuario específico
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessor: Nombre de la acción en español
     */
    public function getActionNameAttribute(): string
    {
        $actions = [
            'create' => 'Creado',
            'update' => 'Actualizado',
            'soft_delete' => 'Eliminado (Lógico)',
            'hard_delete' => 'Eliminado (Permanente)',
            'restore' => 'Restaurado',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Accessor: Nombre del modelo en español
     */
    public function getModelNameAttribute(): string
    {
        $models = [
            'App\Models\User' => 'Usuario',
            'App\Models\Product' => 'Producto',
            'App\Models\Sale' => 'Venta',
            'App\Models\Category' => 'Categoría',
        ];

        return $models[$this->model_type] ?? class_basename($this->model_type);
    }
}
