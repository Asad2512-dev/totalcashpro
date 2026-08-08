<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CashDrawerMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CashDrawerTransaction extends Model
{
    protected $fillable = [
        'cash_drawer_id', 'cash_drawer_session_id',
        'organization_id', 'branch_id',
        'type', 'amount', 'description',
        'reference_type', 'reference_id', 'reason',
        'paired_transaction_id', 'transfer_drawer_id',
        'approval_status', 'cash_up_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => CashDrawerMovementType::class,
        ];
    }

    public function cashDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(CashDrawerSession::class, 'cash_drawer_session_id');
    }

    public function transferDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class, 'transfer_drawer_id');
    }

    public function pairedTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'paired_transaction_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signedAmount(): float
    {
        $amount = (float) $this->amount;
        $type = $this->type instanceof CashDrawerMovementType
            ? $this->type
            : CashDrawerMovementType::from((string) $this->type);

        return match ($type) {
            CashDrawerMovementType::Sale,
            CashDrawerMovementType::Deposit,
            CashDrawerMovementType::FloatAdd => $amount,
            CashDrawerMovementType::Expense,
            CashDrawerMovementType::Withdrawal,
            CashDrawerMovementType::Refund,
            CashDrawerMovementType::FloatRemove => -$amount,
            CashDrawerMovementType::Transfer => -$amount,
            CashDrawerMovementType::Adjustment => $amount,
        };
    }
}
