<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RecurringBillGenerated;
use App\Models\Bill;
use App\Models\User;
use App\Notifications\InvoiceGeneratedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class NotifyRecurringBillGenerated implements ShouldQueue
{
    public function handle(RecurringBillGenerated $event): void
    {
        $bill = Bill::query()->find($event->billId);

        if ($bill === null) {
            return;
        }

        $bill->loadMissing('organization');

        $admin = User::query()
            ->where('organization_id', $bill->organization_id)
            ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
            ->first();

        $admin?->notify(new InvoiceGeneratedNotification($bill));
    }
}
