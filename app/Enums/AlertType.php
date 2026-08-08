<?php

declare(strict_types=1);

namespace App\Enums;

enum AlertType: string
{
    case OverdueBill = 'overdue_bill';
    case CashVariance = 'cash_variance';
    case LowStock = 'low_stock';
    case LateDelivery = 'late_delivery';
    case MissingClockOut = 'missing_clock_out';
    case SupplierPriceIncrease = 'supplier_price_increase';
    case PayrollDue = 'payroll_due';
    case InvoiceMismatch = 'invoice_mismatch';
    case UnfilledShift = 'unfilled_shift';

    public function label(): string
    {
        return match ($this) {
            self::OverdueBill => 'Overdue bill',
            self::CashVariance => 'Cash variance',
            self::LowStock => 'Low stock',
            self::LateDelivery => 'Late delivery',
            self::MissingClockOut => 'Missing clock-out',
            self::SupplierPriceIncrease => 'Supplier price increase',
            self::PayrollDue => 'Payroll due',
            self::InvoiceMismatch => 'Invoice mismatch',
            self::UnfilledShift => 'Unfilled shift',
        };
    }
}
