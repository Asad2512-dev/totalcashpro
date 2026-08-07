<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleSlug;
use App\Enums\TwoFactorMethod;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens;
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
        'onboarding_completed_at',
        'pin_hash',
        'hourly_rate',
        'notes',
        'address',
        'two_factor_enabled',
        'two_factor_method',
        'two_factor_confirmed_at',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'pin_hash',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_method' => TwoFactorMethod::class,
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

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function securityLogs(): HasMany
    {
        return $this->hasMany(SecurityLog::class);
    }

    public function recoveryCodes(): HasMany
    {
        return $this->hasMany(TwoFactorRecoveryCode::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
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

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function hasPinConfigured(): bool
    {
        return $this->pin_hash !== null && $this->pin_hash !== '';
    }
}
