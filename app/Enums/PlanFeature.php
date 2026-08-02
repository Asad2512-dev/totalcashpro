<?php

declare(strict_types=1);

namespace App\Enums;

enum PlanFeature: string
{
    case CashUp = 'cash_up';
    case Attendance = 'attendance';
    case Reports = 'reports';
    case Inventory = 'inventory';
    case Payroll = 'payroll';
    case Rota = 'rota';
    case Suppliers = 'suppliers';
    case AdvancedReports = 'advanced_reports';
    case MultipleBranches = 'multiple_branches';
    case StaffPanel = 'staff_panel';

    public function label(): string
    {
        return match ($this) {
            self::CashUp => 'Cash Up',
            self::Attendance => 'Attendance',
            self::Reports => 'Reports',
            self::Inventory => 'Inventory',
            self::Payroll => 'Payroll',
            self::Rota => 'Staff Rota',
            self::Suppliers => 'Suppliers',
            self::AdvancedReports => 'Advanced Reports',
            self::MultipleBranches => 'Multiple Branches',
            self::StaffPanel => 'Staff Panel',
        };
    }

    /**
     * @return list<self>
     */
    public static function moduleFeatures(): array
    {
        return [
            self::CashUp,
            self::Attendance,
            self::Reports,
            self::Inventory,
            self::Payroll,
            self::Rota,
            self::Suppliers,
            self::AdvancedReports,
            self::MultipleBranches,
            self::StaffPanel,
        ];
    }
}
