<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CashDrawerSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CashDrawerSession extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'cash_drawer_id',
        'opened_by_user_id', 'closed_by_user_id',
        'opened_at', 'closed_at',
        'opening_float', 'opening_count', 'closing_count',
        'expected_cash', 'actual_cash', 'variance',
        'variance_reason', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'opening_float' => 'decimal:2',
            'opening_count' => 'array',
            'closing_count' => 'array',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'variance' => 'decimal:2',
            'status' => CashDrawerSessionStatus::class,
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

    public function cashDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by_user_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashDrawerTransaction::class, 'cash_drawer_session_id');
    }

    public function isOpen(): bool
    {
        return $this->status === CashDrawerSessionStatus::Open;
    }
}
