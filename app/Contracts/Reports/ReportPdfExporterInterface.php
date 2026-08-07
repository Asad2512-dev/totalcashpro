<?php

declare(strict_types=1);

namespace App\Contracts\Reports;

/**
 * Future PDF export hook for the Reports Center.
 */
interface ReportPdfExporterInterface
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function export(array $report, string $filename): string;
}
