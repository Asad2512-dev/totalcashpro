<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BusinessAdmin\RecurringBillService;
use Illuminate\Console\Command;

final class GenerateRecurringBills extends Command
{
    protected $signature = 'finance:generate-recurring-bills';

    protected $description = 'Generate bills from active recurring bill templates';

    public function handle(RecurringBillService $service): int
    {
        $count = $service->generateDueBills();
        $this->info("Generated {$count} recurring bill(s).");

        return self::SUCCESS;
    }
}
