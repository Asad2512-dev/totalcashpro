<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleSlug;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_path',
        'password',
        'role_id',
        'organization_id',
        'branch_id',
        'status',
        'last_login_at',
        'pin_code',
        'hourly_rate',
        'notes',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === RoleSlug::SuperAdmin->value;
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === RoleSlug::Admin->value;
    }

    public function isStaff(): bool
    {
        return $this->role?->slug === RoleSlug::Staff->value;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
