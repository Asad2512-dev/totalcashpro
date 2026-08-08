<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CashDrawerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class CashDrawer extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'name', 'code',
        'opening_balance', 'current_balance', 'currency',
        'finance_bank_account_id', 'is_active', 'status', 'notes',
        'assigned_user_id', 'last_opened_at', 'last_closed_at', 'last_cash_up_at',
        'settings', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
            'status' => CashDrawerStatus::class,
            'last_opened_at' => 'datetime',
            'last_closed_at' => 'datetime',
            'last_cash_up_at' => 'datetime',
            'settings' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CashDrawerSession::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashDrawerTransaction::class);
    }

    public function cashUps(): HasMany
    {
        return $this->hasMany(CashUp::class);
    }

    public function lastCashUp(): HasOne
    {
        return $this->hasOne(CashUp::class)->latestOfMany('cashup_date');
    }

    public function openSession(): HasOne
    {
        return $this->hasOne(CashDrawerSession::class)
            ->where('status', \App\Enums\CashDrawerSessionStatus::Open->value)
            ->latest('opened_at');
    }

    public function defaultOpeningFloat(?Organization $organization = null): float
    {
        $configured = (float) ($this->settings['default_opening_float'] ?? 0);

        if ($configured > 0) {
            return $configured;
        }

        if ((float) $this->opening_balance > 0) {
            return (float) $this->opening_balance;
        }

        $org = $organization ?? $this->organization;
        $orgFloat = (float) data_get($org?->settings, 'cash.default_opening_float', 0);

        if ($orgFloat > 0) {
            return $orgFloat;
        }

        return (float) config('cash.default_opening_float', 100);
    }

    public function varianceThreshold(): float
    {
        return (float) ($this->settings['variance_threshold'] ?? config('cash.default_variance_threshold', 0));
    }

    public function drawerStatus(): CashDrawerStatus
    {
        if ($this->status instanceof CashDrawerStatus) {
            return $this->status;
        }

        if (! $this->is_active) {
            return CashDrawerStatus::Inactive;
        }

        return CashDrawerStatus::tryFrom((string) $this->status) ?? CashDrawerStatus::Active;
    }

    public function isUsableForCashUp(): bool
    {
        return $this->drawerStatus()->isUsableForCashUp();
    }
}
