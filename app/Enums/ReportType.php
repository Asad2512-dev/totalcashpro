<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportType: string
{
    case Overview = 'overview';
    case CashUp = 'cash_up';
    case Sales = 'sales';
    case Attendance = 'attendance';
    case Staff = 'staff';
    case Inventory = 'inventory';
    case Payroll = 'payroll';
    case Expenses = 'expenses';
    case Bills = 'bills';
    case Suppliers = 'suppliers';
    case ProfitLoss = 'profit_loss';
    case CashFlow = 'cash_flow';
    case Vat = 'vat';

    public function label(): string
    {
        return match ($this) {
            self::Overview => 'Overview',
            self::CashUp => 'Cash up',
            self::Sales => 'Sales',
            self::Attendance => 'Attendance',
            self::Staff => 'Staff',
            self::Inventory => 'Inventory',
            self::Payroll => 'Payroll',
            self::Expenses => 'Expenses',
            self::Bills => 'Bills',
            self::Suppliers => 'Suppliers',
            self::ProfitLoss => 'Profit & loss',
            self::CashFlow => 'Cash flow',
            self::Vat => 'VAT',
        };
    }
}
