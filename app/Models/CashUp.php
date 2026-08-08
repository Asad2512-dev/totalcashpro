<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CashUpShift;
use App\Enums\CashUpStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CashUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'cash_drawer_id',
        'cash_drawer_session_id',
        'cashup_date',
        'shift',
        'opening_float',
        'opening_float_count',
        'cash_sales_total',
        'coins_total',
        'coins_detail',
        'notes_total',
        'notes_detail',
        'cards_total',
        'cards_detail',
        'expenses_total',
        'expenses_detail',
        'online_orders_total',
        'online_orders_detail',
        'platform_deductions_total',
        'platform_deductions_detail',
        'expected_cash',
        'actual_cash',
        'variance',
        'variance_reason',
        'status',
        'approved_by_user_id',
        'approved_at',
        'locked_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'cashup_date' => 'date',
            'shift' => CashUpShift::class,
            'opening_float' => 'decimal:2',
            'opening_float_count' => 'array',
            'cash_sales_total' => 'decimal:2',
            'coins_total' => 'decimal:2',
            'coins_detail' => 'array',
            'notes_total' => 'decimal:2',
            'notes_detail' => 'array',
            'cards_total' => 'decimal:2',
            'cards_detail' => 'array',
            'expenses_total' => 'decimal:2',
            'expenses_detail' => 'array',
            'online_orders_total' => 'decimal:2',
            'online_orders_detail' => 'array',
            'platform_deductions_total' => 'decimal:2',
            'platform_deductions_detail' => 'array',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'variance' => 'decimal:2',
            'status' => CashUpStatus::class,
            'approved_at' => 'datetime',
            'locked_at' => 'datetime',
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

    public function cashDrawerSession(): BelongsTo
    {
        return $this->belongsTo(CashDrawerSession::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function grossTotal(): float
    {
        return (float) $this->coins_total
            + (float) $this->notes_total
            + (float) $this->cards_total
            + (float) $this->online_orders_total;
    }

    public function netTotal(): float
    {
        return $this->grossTotal()
            - (float) $this->expenses_total
            - (float) $this->platform_deductions_total;
    }

    public function physicalCashTotal(): float
    {
        if ($this->actual_cash !== null) {
            return (float) $this->actual_cash;
        }

        return round((float) $this->coins_total + (float) $this->notes_total, 2);
    }

    public function legacyFloatInNotes(): float
    {
        foreach ($this->notes_detail ?? [] as $row) {
            if (($row['note'] ?? '') === 'Extra Coin (Float)') {
                return (float) ($row['amount'] ?? 0);
            }
        }

        return 0.0;
    }

    public function effectiveOpeningFloat(): float
    {
        if ((float) $this->opening_float > 0) {
            return (float) $this->opening_float;
        }

        return $this->legacyFloatInNotes();
    }

    public function cashSalesTotal(): float
    {
        if ((float) $this->cash_sales_total > 0) {
            return (float) $this->cash_sales_total;
        }

        return max(0, round(
            $this->physicalCashTotal() - $this->effectiveOpeningFloat() + (float) $this->expenses_total,
            2,
        ));
    }

    /**
     * Gross sales revenue for finance/P&L — excludes opening float.
     * Cash expenses are recorded separately; do not subtract them here.
     */
    public function revenueTotal(): float
    {
        return round(
            $this->cashSalesTotal()
            + (float) $this->cards_total
            + (float) $this->online_orders_total
            - (float) $this->platform_deductions_total,
            2,
        );
    }

    public function calculatedExpectedCash(): float
    {
        if ($this->expected_cash !== null) {
            return (float) $this->expected_cash;
        }

        return round(
            $this->effectiveOpeningFloat()
            + $this->cashSalesTotal()
            - (float) $this->expenses_total,
            2,
        );
    }

    public function varianceAmount(): float
    {
        if ($this->variance !== null) {
            return (float) $this->variance;
        }

        return round($this->physicalCashTotal() - $this->calculatedExpectedCash(), 2);
    }

    public function isLocked(): bool
    {
        $status = $this->status instanceof CashUpStatus
            ? $this->status
            : CashUpStatus::tryFrom((string) $this->status);

        return in_array($status, [CashUpStatus::Locked, CashUpStatus::Approved], true)
            || $this->locked_at !== null;
    }
}
