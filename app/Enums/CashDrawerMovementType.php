<?php

declare(strict_types=1);

namespace App\Enums;

enum CashDrawerMovementType: string
{
    case Sale = 'sale';
    case Expense = 'expense';
    case Withdrawal = 'withdrawal';
    case Deposit = 'deposit';
    case Adjustment = 'adjustment';
    case Refund = 'refund';
    case FloatAdd = 'float_add';
    case FloatRemove = 'float_remove';
    case Transfer = 'transfer';

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Cash sale',
            self::Expense => 'Expense',
            self::Withdrawal => 'Withdrawal',
            self::Deposit => 'Deposit',
            self::Adjustment => 'Adjustment',
            self::Refund => 'Refund',
            self::FloatAdd => 'Float addition',
            self::FloatRemove => 'Float removal',
            self::Transfer => 'Transfer',
        };
    }

    public function affectsExpectedCash(): bool
    {
        return match ($this) {
            self::Sale, self::Deposit, self::FloatAdd => true,
            self::Expense, self::Withdrawal, self::Refund, self::FloatRemove => true,
            self::Transfer, self::Adjustment => true,
        };
    }
}
