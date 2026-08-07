<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\RecurringBill;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class RecurringBillGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly RecurringBill $recurringBill,
        public readonly int $billId,
    ) {}
}
