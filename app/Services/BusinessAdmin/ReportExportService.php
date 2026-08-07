<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportExportService implements ServiceInterface
{
    /**
     * @param  array{columns: list<string>, rows: list<array<int, string>>}  $table
     */
    public function csv(array $table, string $filename = 'report.csv'): StreamedResponse
    {
        return response()->streamDownload(function () use ($table): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fputcsv($handle, $table['columns']);
            foreach ($table['rows'] as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Excel-compatible tab-separated export (opens in Excel without extra packages).
     *
     * @param  array{columns: list<string>, rows: list<array<int, string>>}  $table
     */
    public function excel(array $table, string $filename = 'report.xls'): StreamedResponse
    {
        return response()->streamDownload(function () use ($table): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $table['columns'], "\t");
            foreach ($table['rows'] as $row) {
                fputcsv($handle, $row, "\t");
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }
}
