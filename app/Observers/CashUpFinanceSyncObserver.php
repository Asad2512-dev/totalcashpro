<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceStatus;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;

final class CashUpFinanceSyncObserver
{
    public function saved(CashUp $cashUp): void
    {
        if ((float) $cashUp->netTotal() <= 0) {
            return;
        }

        FinanceIncomeEntry::query()->updateOrCreate(
            [
                'organization_id' => $cashUp->organization_id,
                'reference_type' => CashUp::class,
                'reference_id' => $cashUp->id,
            ],
            [
                'branch_id' => $cashUp->branch_id,
                'source' => FinanceIncomeSource::CashUp->value,
                'title' => sprintf(
                    'Cash up %s · %s',
                    $cashUp->cashup_date?->format('d M Y'),
                    $cashUp->shift instanceof \BackedEnum ? $cashUp->shift->value : (string) $cashUp->shift,
                ),
                'net_amount' => $cashUp->netTotal(),
                'vat_amount' => 0,
                'gross_amount' => $cashUp->netTotal(),
                'income_date' => $cashUp->cashup_date,
                'status' => FinanceStatus::Approved->value,
                'approved_at' => now(),
                'created_by' => $cashUp->created_by,
            ],
        );
    }
}
