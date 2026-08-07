<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AttendanceLog;
use App\Models\Bill;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;
use App\Models\FinanceSupplierPayment;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Spending;
use App\Models\SupplierInvoice;
use App\Models\Wage;
use App\Support\Reports\ReportCenterCache;

final class ReportCenterCacheObserver
{
    public function saved(CashUp|Bill|Spending|Wage|FinanceIncomeEntry|FinanceSupplierPayment|SupplierInvoice|InventoryCount|InventoryItem|AttendanceLog|PurchaseOrder $model): void
    {
        $this->bump($model->organization_id ?? null);
    }

    public function deleted(CashUp|Bill|Spending|Wage|FinanceIncomeEntry|FinanceSupplierPayment|SupplierInvoice|InventoryCount|InventoryItem|AttendanceLog|PurchaseOrder $model): void
    {
        $this->bump($model->organization_id ?? null);
    }

    private function bump(mixed $organizationId): void
    {
        if ($organizationId === null) {
            return;
        }

        ReportCenterCache::bump((int) $organizationId);
    }
}
