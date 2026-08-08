<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\SupplierStatus;
use App\Enums\DeliveryStatus;
use App\Models\Delivery;
use App\Models\ProcurementSetting;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\SupplierInvoice;
use App\Models\User;
use Illuminate\Support\Collection;

final class SupplierPerformanceService implements ServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function metricsForSupplier(User $user, Supplier $supplier): array
    {
        abort_unless((int) $supplier->organization_id === (int) $user->organization_id, 403);

        $orders = PurchaseOrder::query()
            ->where('supplier_id', $supplier->id)
            ->where('organization_id', $user->organization_id)
            ->get();

        $deliveries = Delivery::query()
            ->whereHas('purchaseOrder', fn ($q) => $q->where('supplier_id', $supplier->id))
            ->get();

        $onTime = $deliveries->filter(function (Delivery $d) {
            return $d->delivered_at
                && $d->expected_delivery_at
                && $d->delivered_at->lte($d->expected_delivery_at);
        })->count();

        $totalDeliveries = $deliveries->whereNotNull('delivered_at')->count();

        $spend = (float) SupplierInvoice::query()
            ->where('supplier_id', $supplier->id)
            ->where('organization_id', $user->organization_id)
            ->sum('gross_amount');

        return [
            'orders' => $orders->count(),
            'on_time_deliveries' => $onTime,
            'late_deliveries' => max(0, $totalDeliveries - $onTime),
            'failed_deliveries' => $deliveries->where('status', DeliveryStatus::Failed)->count(),
            'total_spend' => $spend,
            'average_order_value' => $orders->count() > 0 ? round($spend / $orders->count(), 2) : 0,
            'on_time_rate' => $totalDeliveries > 0 ? round(($onTime / $totalDeliveries) * 100, 1) : 0,
        ];
    }
}
