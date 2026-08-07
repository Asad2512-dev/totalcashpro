<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\GoodsReceivedNote;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class PurchaseOrderReceived
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly PurchaseOrder $purchaseOrder,
        public readonly GoodsReceivedNote $goodsReceivedNote,
        public readonly User $actor,
    ) {}
}
