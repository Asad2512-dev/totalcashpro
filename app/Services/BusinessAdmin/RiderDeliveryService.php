<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\DeliveryPriority;
use App\Enums\DeliveryStatus;
use App\Models\Delivery;
use App\Models\DeliveryEvent;
use App\Models\PurchaseOrder;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class RiderDeliveryService implements ServiceInterface
{
    public function assign(
        User $admin,
        PurchaseOrder $po,
        Rider $rider,
        ?string $expectedPickup = null,
        ?string $expectedDelivery = null,
        ?string $notes = null,
        DeliveryPriority $priority = DeliveryPriority::Normal,
    ): Delivery {
        if ((int) $po->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        if ((int) $rider->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }

        if (! $rider->servesBranch((int) $po->branch_id)) {
            throw ValidationException::withMessages(['rider' => 'Rider is not assigned to this branch.']);
        }

        $delivery = Delivery::query()->updateOrCreate(
            ['purchase_order_id' => $po->id],
            [
                'organization_id' => $po->organization_id,
                'branch_id' => $po->branch_id,
                'rider_id' => $rider->id,
                'status' => DeliveryStatus::Assigned->value,
                'priority' => $priority->value,
                'expected_pickup_at' => $expectedPickup,
                'expected_delivery_at' => $expectedDelivery,
                'notes' => $notes,
                'assigned_by' => $admin->id,
            ],
        );

        $this->logEvent($delivery, 'assigned', $admin, $notes);

        return $delivery;
    }

    public function accept(Rider $rider, Delivery $delivery): Delivery
    {
        $this->assertRiderOwns($rider, $delivery);
        $this->assertStatus($delivery, [DeliveryStatus::Assigned]);

        $delivery->update([
            'status' => DeliveryStatus::Accepted->value,
            'accepted_at' => now(),
        ]);

        return $this->logEvent($delivery, 'accepted', $rider->user)->refresh();
    }

    public function reject(Rider $rider, Delivery $delivery, string $reason): Delivery
    {
        $this->assertRiderOwns($rider, $delivery);
        $this->assertStatus($delivery, [DeliveryStatus::Assigned]);

        $delivery->update([
            'status' => DeliveryStatus::Cancelled->value,
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);

        return $this->logEvent($delivery, 'rejected', $rider->user, $reason)->refresh();
    }

    public function confirmPickup(
        Rider $rider,
        Delivery $delivery,
        ?float $collectedQty = null,
        ?string $discrepancyReason = null,
    ): Delivery {
        $this->assertRiderOwns($rider, $delivery);

        $updates = [
            'status' => DeliveryStatus::Collected->value,
            'collected_at' => now(),
        ];

        if ($collectedQty !== null) {
            $updates['pickup_discrepancy_qty'] = $collectedQty;
        }
        if ($discrepancyReason) {
            $updates['pickup_discrepancy_reason'] = $discrepancyReason;
        }

        $delivery->update($updates);

        return $this->logEvent($delivery, 'collected', $rider->user, $discrepancyReason)->refresh();
    }

    /**
     * @return Collection<int, Delivery>
     */
    public function todayForRider(Rider $rider): Collection
    {
        return Delivery::query()
            ->with(['purchaseOrder.supplier', 'purchaseOrder.lines', 'branch'])
            ->where('rider_id', $rider->id)
            ->whereDate('created_at', today())
            ->whereNotIn('status', [DeliveryStatus::Cancelled->value])
            ->orderBy('expected_delivery_at')
            ->get();
    }

    /**
     * @return array<string, int>
     */
    public function statsForRider(Rider $rider): array
    {
        $today = $this->todayForRider($rider);

        return [
            'assigned' => $today->where('status', DeliveryStatus::Assigned)->count(),
            'accepted' => $today->where('status', DeliveryStatus::Accepted)->count(),
            'pickup' => $today->whereIn('status', [DeliveryStatus::AtSupplier, DeliveryStatus::Collected])->count(),
            'in_transit' => $today->whereIn('status', [DeliveryStatus::OutForDelivery, DeliveryStatus::Arrived])->count(),
            'delivered' => $today->where('status', DeliveryStatus::Delivered)->count(),
            'failed' => $today->where('status', DeliveryStatus::Failed)->count(),
            'overdue' => $today->filter(fn (Delivery $d) => $d->expected_delivery_at && $d->expected_delivery_at->isPast() && $d->status !== DeliveryStatus::Delivered)->count(),
        ];
    }

    public function advanceStatus(Rider $rider, Delivery $delivery, DeliveryStatus $status, ?string $notes = null): Delivery
    {
        $this->assertRiderOwns($rider, $delivery);

        $updates = ['status' => $status->value];

        match ($status) {
            DeliveryStatus::Accepted => $updates['accepted_at'] = now(),
            DeliveryStatus::AtSupplier => $updates['at_supplier_at'] = now(),
            DeliveryStatus::Collected => $updates['collected_at'] = now(),
            DeliveryStatus::OutForDelivery => $updates['out_for_delivery_at'] = now(),
            DeliveryStatus::Arrived => $updates['arrived_at'] = now(),
            DeliveryStatus::Delivered => $updates = array_merge($updates, [
                'delivered_at' => now(),
                'awaiting_receiving' => true,
            ]),
            DeliveryStatus::Failed => $updates = array_merge($updates, [
                'failed_at' => now(),
                'failure_reason' => $notes,
            ]),
            default => null,
        };

        if ($notes !== null && $status !== DeliveryStatus::Failed) {
            $updates['delivery_notes'] = $notes;
        }

        $delivery->update($updates);

        return $this->logEvent($delivery, $status->value, $rider->user, $notes)->refresh();
    }

    private function logEvent(Delivery $delivery, string $event, ?User $user = null, ?string $notes = null): Delivery
    {
        DeliveryEvent::query()->create([
            'delivery_id' => $delivery->id,
            'event' => $event,
            'notes' => $notes,
            'created_by' => $user?->id,
        ]);

        return $delivery;
    }

    private function assertRiderOwns(Rider $rider, Delivery $delivery): void
    {
        if ((int) $delivery->rider_id !== (int) $rider->id) {
            abort(403);
        }
    }

    /**
     * @param  list<DeliveryStatus>  $allowed
     */
    private function assertStatus(Delivery $delivery, array $allowed): void
    {
        if (! in_array($delivery->status, $allowed, true)) {
            throw ValidationException::withMessages(['delivery' => 'Invalid delivery status for this action.']);
        }
    }
}
