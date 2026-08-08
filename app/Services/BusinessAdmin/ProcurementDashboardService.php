<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\DeliveryStatus;
use App\Enums\PurchaseOrderStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\SupplierStatus;
use App\Models\Delivery;
use App\Models\InvoiceMatch;
use App\Models\ProcurementSetting;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\User;
use Illuminate\Support\Collection;

final class ProcurementDashboardService implements ServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function summary(User $user, ?int $branchId = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId ??= app(BranchContext::class)->currentBranchId($user);

        $poQuery = PurchaseOrder::query()->where('organization_id', $orgId);
        if ($branchId) {
            $poQuery->where('branch_id', $branchId);
        }

        $invoiceQuery = SupplierInvoice::query()->where('organization_id', $orgId);
        if ($branchId) {
            $invoiceQuery->where('branch_id', $branchId);
        }

        $topSupplierRows = SupplierInvoice::query()
            ->selectRaw('supplier_id, SUM(gross_amount) as spend')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->groupBy('supplier_id')
            ->orderByDesc('spend')
            ->limit(5)
            ->get();

        $topSuppliers = $topSupplierRows->map(function ($row) {
            $row->supplier = Supplier::query()->find($row->supplier_id);

            return $row;
        });

        return [
            'total_suppliers' => Supplier::query()->where('organization_id', $orgId)->count(),
            'active_suppliers' => Supplier::query()->where('organization_id', $orgId)->where('status', SupplierStatus::Active->value)->count(),
            'awaiting_approval' => (clone $poQuery)->where('status', PurchaseOrderStatus::Pending->value)->count(),
            'awaiting_pickup' => Delivery::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->whereIn('status', [DeliveryStatus::Assigned->value, DeliveryStatus::Accepted->value])
                ->count(),
            'in_transit' => Delivery::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->whereIn('status', [DeliveryStatus::OutForDelivery->value, DeliveryStatus::Arrived->value, DeliveryStatus::Collected->value])
                ->count(),
            'awaiting_receiving' => Delivery::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->where('awaiting_receiving', true)
                ->count(),
            'partial_receipts' => (clone $poQuery)->where('status', PurchaseOrderStatus::PartiallyReceived->value)->count(),
            'disputed_invoices' => (clone $invoiceQuery)->where('status', SupplierInvoiceStatus::Disputed->value)->count(),
            'outstanding_bills' => (clone $invoiceQuery)->whereIn('status', [
                SupplierInvoiceStatus::Outstanding->value,
                SupplierInvoiceStatus::PartiallyPaid->value,
            ])->sum('gross_amount'),
            'month_spend' => (clone $invoiceQuery)
                ->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->sum('gross_amount'),
            'top_suppliers' => $topSuppliers,
            'settings' => ProcurementSetting::forOrganization($orgId),
        ];
    }
}
