<?php

declare(strict_types=1);

namespace App\Enums;

enum InventoryTransactionType: string
{
    case Opening = 'opening';
    case Purchase = 'purchase';
    case Receipt = 'receipt';
    case Consumption = 'consumption';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case Waste = 'waste';
    case Adjustment = 'adjustment';
    case Stocktake = 'stocktake';
    case Return = 'return';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::Opening => 'Opening',
            self::Purchase => 'Purchase',
            self::Receipt => 'Receipt',
            self::Consumption => 'Consumption',
            self::TransferIn => 'Transfer in',
            self::TransferOut => 'Transfer out',
            self::Waste => 'Waste',
            self::Adjustment => 'Adjustment',
            self::Stocktake => 'Stocktake',
            self::Return => 'Return',
            self::Damaged => 'Damaged',
        };
    }
}
