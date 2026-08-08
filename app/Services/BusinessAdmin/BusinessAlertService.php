<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\AlertPriority;
use App\Enums\AlertStatus;
use App\Enums\AlertType;
use App\Enums\BillStatus;
use App\Enums\DeliveryStatus;
use App\Models\Bill;
use App\Models\BusinessAlert;
use App\Models\BusinessAlertRule;
use App\Models\CashUp;
use App\Models\Delivery;
use App\Models\InventoryItem;
use App\Models\Organization;
use App\Models\SupplierPriceHistory;
use App\Models\User;
use App\Models\Wage;
use App\Enums\WageStatus;
use Illuminate\Support\Collection;

final class BusinessAlertService implements ServiceInterface
{
    public function openForOrganization(User $user, ?int $branchId = null, int $limit = 20): Collection
    {
        return BusinessAlert::query()
            ->where('organization_id', $user->organization_id)
            ->where('status', AlertStatus::Open->value)
            ->when($branchId, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('branch_id')->orWhere('branch_id', $branchId)))
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 ELSE 5 END")
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function raise(
        int $organizationId,
        AlertType $type,
        string $title,
        string $message,
        AlertPriority $priority = AlertPriority::Medium,
        ?int $branchId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $actionUrl = null,
        ?array $metadata = null,
    ): ?BusinessAlert {
        $exists = BusinessAlert::query()
            ->where('organization_id', $organizationId)
            ->where('alert_type', $type->value)
            ->where('status', AlertStatus::Open->value)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($referenceType, fn ($q) => $q->where('reference_type', $referenceType))
            ->when($referenceId, fn ($q) => $q->where('reference_id', $referenceId))
            ->exists();

        if ($exists) {
            return null;
        }

        return BusinessAlert::query()->create([
            'organization_id' => $organizationId,
            'branch_id' => $branchId,
            'alert_type' => $type->value,
            'priority' => $priority->value,
            'status' => AlertStatus::Open->value,
            'title' => $title,
            'message' => $message,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'action_url' => $actionUrl,
            'metadata' => $metadata,
        ]);
    }

    public function resolve(User $user, BusinessAlert $alert): BusinessAlert
    {
        abort_unless((int) $alert->organization_id === (int) $user->organization_id, 403);

        $alert->update([
            'status' => AlertStatus::Resolved->value,
            'resolved_at' => now(),
            'resolved_by' => $user->id,
        ]);

        return $alert->refresh();
    }

    public function acknowledge(User $user, BusinessAlert $alert): BusinessAlert
    {
        abort_unless((int) $alert->organization_id === (int) $user->organization_id, 403);

        $alert->update([
            'status' => AlertStatus::Acknowledged->value,
            'acknowledged_at' => now(),
            'acknowledged_by' => $user->id,
        ]);

        return $alert->refresh();
    }

    public function resolveByReference(int $organizationId, AlertType $type, string $referenceType, int $referenceId): void
    {
        BusinessAlert::query()
            ->where('organization_id', $organizationId)
            ->where('alert_type', $type->value)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('status', AlertStatus::Open->value)
            ->update([
                'status' => AlertStatus::Resolved->value,
                'resolved_at' => now(),
            ]);
    }

    public function rule(int $organizationId, string $ruleType, ?int $branchId = null): BusinessAlertRule
    {
        return BusinessAlertRule::query()->firstOrCreate(
            [
                'organization_id' => $organizationId,
                'branch_id' => $branchId,
                'rule_type' => $ruleType,
            ],
            $this->defaultRuleValues($ruleType),
        );
    }

    public function generateForOrganization(Organization $organization): int
    {
        $count = 0;
        $orgId = (int) $organization->id;

        $varianceRule = $this->rule($orgId, AlertType::CashVariance->value);
        if ($varianceRule->is_active) {
            $threshold = (float) ($varianceRule->threshold_value ?? config('cash.default_variance_threshold', 5));
            $cashUps = CashUp::query()
                ->where('organization_id', $orgId)
                ->whereDate('cashup_date', '>=', now()->subDays(7))
                ->get();

            foreach ($cashUps as $cashUp) {
                $variance = abs((float) $cashUp->platform_deductions_total);
                if ($variance >= $threshold) {
                    $alert = $this->raise(
                        $orgId,
                        AlertType::CashVariance,
                        'Cash variance detected',
                        sprintf('Cash-up on %s has variance of £%s (threshold £%s).', $cashUp->cashup_date?->format('d M Y'), number_format($variance, 2), number_format($threshold, 2)),
                        $variance >= $threshold * 2 ? AlertPriority::High : AlertPriority::Medium,
                        (int) $cashUp->branch_id,
                        CashUp::class,
                        (int) $cashUp->id,
                        route('business-admin.cash-history.show', $cashUp, false),
                    );
                    if ($alert) {
                        $count++;
                    }
                }
            }
        }

        $lowStockRule = $this->rule($orgId, AlertType::LowStock->value);
        if ($lowStockRule->is_active) {
            $items = InventoryItem::query()
                ->where('organization_id', $orgId)
                ->whereRaw('(par_level > 0 AND stock_total_pcs < par_level) OR (stock_limit > 0 AND stock_total_pcs <= stock_limit)')
                ->get();

            foreach ($items as $item) {
                $min = (float) ($item->min_level > 0 ? $item->min_level : ($item->par_level > 0 ? $item->par_level : $item->stock_limit));
                if ($min > 0 && (float) $item->stock_total_pcs < $min) {
                    $alert = $this->raise(
                        $orgId,
                        AlertType::LowStock,
                        'Low stock: '.$item->name,
                        sprintf('%s is at %s (minimum %s).', $item->name, $item->stock_total_pcs, $min),
                        AlertPriority::High,
                        (int) $item->branch_id,
                        InventoryItem::class,
                        (int) $item->id,
                        route('business-admin.inventory', absolute: false),
                    );
                    if ($alert) {
                        $count++;
                    }
                } else {
                    $this->resolveByReference($orgId, AlertType::LowStock, InventoryItem::class, (int) $item->id);
                }
            }

            $this->resolveReplenishedLowStockAlerts($orgId);
        }

        $overdueBills = Bill::query()
            ->where('organization_id', $orgId)
            ->whereIn('status', [BillStatus::Pending->value, BillStatus::Approved->value, BillStatus::Overdue->value])
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();

        foreach ($overdueBills as $bill) {
            $alert = $this->raise(
                $orgId,
                AlertType::OverdueBill,
                'Overdue bill: '.$bill->title,
                sprintf('£%s due %s.', number_format((float) $bill->gross_amount, 2), $bill->due_date?->format('d M Y')),
                AlertPriority::High,
                (int) $bill->branch_id,
                Bill::class,
                (int) $bill->id,
                route('business-admin.finance.bills', absolute: false),
            );
            if ($alert) {
                $count++;
            }
        }

        $lateDeliveries = Delivery::query()
            ->where('organization_id', $orgId)
            ->whereNotIn('status', [DeliveryStatus::Delivered->value, DeliveryStatus::Cancelled->value, DeliveryStatus::Failed->value])
            ->where('expected_delivery_at', '<', now())
            ->get();

        foreach ($lateDeliveries as $delivery) {
            $alert = $this->raise(
                $orgId,
                AlertType::LateDelivery,
                'Late delivery',
                sprintf('PO %s delivery is overdue.', $delivery->purchaseOrder?->po_number ?? '#'.$delivery->purchase_order_id),
                AlertPriority::Medium,
                (int) $delivery->branch_id,
                Delivery::class,
                (int) $delivery->id,
                route('business-admin.purchase-orders.show', $delivery->purchase_order_id, false),
            );
            if ($alert) {
                $count++;
            }
        }

        $priceRule = $this->rule($orgId, AlertType::SupplierPriceIncrease->value);
        if ($priceRule->is_active) {
            $thresholdPct = (float) ($priceRule->threshold_percent ?? 5);
            $recentChanges = SupplierPriceHistory::query()
                ->where('organization_id', $orgId)
                ->where('effective_from', '>=', now()->subDays(30)->toDateString())
                ->with(['item', 'supplier'])
                ->get()
                ->groupBy(fn ($h) => $h->supplier_id.'-'.$h->inventory_item_id);

            foreach ($recentChanges as $group) {
                $sorted = $group->sortBy('effective_from')->values();
                if ($sorted->count() < 2) {
                    continue;
                }
                $old = (float) $sorted->first()->unit_cost;
                $new = (float) $sorted->last()->unit_cost;
                if ($old <= 0) {
                    continue;
                }
                $increase = (($new - $old) / $old) * 100;
                if ($increase >= $thresholdPct) {
                    $item = $sorted->last()->item;
                    $alert = $this->raise(
                        $orgId,
                        AlertType::SupplierPriceIncrease,
                        'Supplier price increase',
                        sprintf('%s increased from £%s to £%s (+%s%%).', $item?->name ?? 'Product', number_format($old, 2), number_format($new, 2), number_format($increase, 1)),
                        AlertPriority::Medium,
                        null,
                        SupplierPriceHistory::class,
                        (int) $sorted->last()->id,
                        route('business-admin.suppliers.show', $sorted->last()->supplier_id, false),
                        ['increase_percent' => $increase],
                    );
                    if ($alert) {
                        $count++;
                    }
                }
            }
        }

        $pendingPayroll = (float) Wage::query()
            ->where('organization_id', $orgId)
            ->whereIn('status', [WageStatus::Pending->value, WageStatus::Approved->value])
            ->whereBetween('payment_due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->sum('gross_amount');

        if ($pendingPayroll > 0) {
            $alert = $this->raise(
                $orgId,
                AlertType::PayrollDue,
                'Payroll due soon',
                sprintf('£%s payroll due within 7 days.', number_format($pendingPayroll, 2)),
                AlertPriority::Medium,
                null,
                null,
                null,
                route('business-admin.payroll', absolute: false),
            );
            if ($alert) {
                $count++;
            }
        }

        return $count;
    }

    private function resolveReplenishedLowStockAlerts(int $organizationId): void
    {
        $openAlerts = BusinessAlert::query()
            ->where('organization_id', $organizationId)
            ->where('alert_type', AlertType::LowStock->value)
            ->where('status', AlertStatus::Open->value)
            ->where('reference_type', InventoryItem::class)
            ->whereNotNull('reference_id')
            ->get();

        foreach ($openAlerts as $alert) {
            $item = InventoryItem::query()->find($alert->reference_id);
            if ($item === null) {
                $this->resolveByReference($organizationId, AlertType::LowStock, InventoryItem::class, (int) $alert->reference_id);

                continue;
            }

            $min = (float) ($item->min_level > 0 ? $item->min_level : ($item->par_level > 0 ? $item->par_level : $item->stock_limit));
            if ($min <= 0 || (float) $item->stock_total_pcs >= $min) {
                $this->resolveByReference($organizationId, AlertType::LowStock, InventoryItem::class, (int) $item->id);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultRuleValues(string $ruleType): array
    {
        return match ($ruleType) {
            AlertType::CashVariance->value => ['threshold_value' => config('cash.default_variance_threshold', 5), 'is_active' => true],
            AlertType::LowStock->value => ['is_active' => true],
            AlertType::SupplierPriceIncrease->value => ['threshold_percent' => 5, 'is_active' => true],
            default => ['is_active' => true],
        };
    }
}
