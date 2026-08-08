<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\BusinessAdmin\ScheduledReportService;
use Illuminate\Console\Command;

final class RunScheduledReports extends Command
{
    protected $signature = 'reports:run-scheduled';

    protected $description = 'Run due scheduled reports and email recipients';

    public function handle(ScheduledReportService $service): int
    {
        $count = $service->runDueReports();
        $this->info("Ran {$count} scheduled report(s).");

        return self::SUCCESS;
    }
}
