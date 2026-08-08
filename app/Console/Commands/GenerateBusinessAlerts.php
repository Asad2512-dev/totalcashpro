<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\BusinessAdmin\BusinessAlertService;
use Illuminate\Console\Command;

final class GenerateBusinessAlerts extends Command
{
    protected $signature = 'executive:generate-alerts';

    protected $description = 'Generate business alerts for all active organizations';

    public function handle(BusinessAlertService $alerts): int
    {
        $total = 0;

        Organization::query()->where('status', 'active')->chunkById(50, function ($orgs) use ($alerts, &$total): void {
            foreach ($orgs as $org) {
                $total += $alerts->generateForOrganization($org);
            }
        });

        $this->info("Generated {$total} new alerts.");

        return self::SUCCESS;
    }
}
