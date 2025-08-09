<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'deletion_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Accessor: Mapear is_active a status para compatibilidad con API
     */
    public function getStatusAttribute()
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    /**
     * Mutator: Mapear status a is_active para compatibilidad con API
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['is_active'] = $value === 'active';
    }

    /**
     * Relación: El usuario puede tener muchas ventas (si es cajera)
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relación: El usuario puede tener muchas facturas como vendedor
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relación: El usuario puede tener muchas facturas como cliente
     */
    public function customerInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    /**
     * Alias para customerInvoices (compatibilidad con el controlador API)
     */
    public function invoicesAsCustomer(): HasMany
    {
        return $this->customerInvoices();
    }

    /**
     * Accessor: Obtener el nombre del rol principal
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()->name ?? 'Sin rol';
    }

    /**
     * Accessor: Verificar si puede gestionar usuarios
     */
    public function getCanManageUsersAttribute(): bool
    {
        return $this->hasAnyRole(['admin', 'secre']);
    }

    /**
     * Accessor: Verificar si puede gestionar inventario
     */
    public function getCanManageInventoryAttribute(): bool
    {
        return $this->hasRole('bodega');
    }

    /**
     * Accessor: Verificar si puede registrar ventas
     */
    public function getCanMakeSalesAttribute(): bool
    {
        return $this->hasRole('ventas');
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope: Solo usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relación: Usuario que eliminó este registro
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Relación: Usuarios eliminados por este usuario
     */
    public function deletedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'deleted_by');
    }

    /**
     * Relación: Logs de actividad del usuario
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Verificar si el usuario puede ser eliminado
     */
    public function canBeDeleted(): bool
    {
        return $this->sales()->count() === 0 && 
               $this->invoices()->count() === 0 && 
               $this->customerInvoices()->count() === 0;
    }

    /**
     * Obtener el motivo por el cual no se puede eliminar
     */
    public function getDeletionBlockReason(): ?string
    {
        $reasons = [];

        if ($this->sales()->count() > 0) {
            $reasons[] = 'tiene ventas registradas';
        }

        if ($this->invoices()->count() > 0) {
            $reasons[] = 'tiene facturas como vendedor';
        }

        if ($this->customerInvoices()->count() > 0) {
            $reasons[] = 'tiene facturas como cliente';
        }

        return !empty($reasons) ? 'El usuario ' . implode(', ', $reasons) . '.' : null;
    }

    /**
     * Relación con tokens en texto plano (INSEGURO)
     */
    public function plainTextTokens()
    {
        return $this->hasMany(PlainTextToken::class);
    }

    /**
     * Crear un token en texto plano (INSEGURO)
     */
    public function createPlainTextToken(string $name, array $abilities = []): PlainTextToken
    {
        $token = PlainTextToken::generateToken();

        return $this->plainTextTokens()->create([
            'name' => $name,
            'token' => $token, // ALMACENADO EN TEXTO PLANO - INSEGURO
            'abilities' => $abilities,
        ]);
    }

    /**
     * Obtener todos los tokens activos (no expirados)
     */
    public function getActivePlainTextTokens()
    {
        return $this->plainTextTokens()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->get();
    }
}