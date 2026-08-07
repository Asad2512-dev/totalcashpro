<?php

declare(strict_types=1);

namespace App\Support\Reports;

use App\Enums\ReportDatePreset;
use Illuminate\Support\Carbon;

final class ReportDateRangeResolver
{
    /**
     * @return array{from: string, to: string, label: string}
     */
    public static function resolve(ReportDatePreset $preset, ?string $from = null, ?string $to = null): array
    {
        $now = now();

        [$start, $end, $label] = match ($preset) {
            ReportDatePreset::Today => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Today'],
            ReportDatePreset::Yesterday => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(), 'Yesterday'],
            ReportDatePreset::Last7Days => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 'Last 7 days'],
            ReportDatePreset::Last30Days => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay(), 'Last 30 days'],
            ReportDatePreset::ThisWeek => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'This week'],
            ReportDatePreset::LastWeek => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek(), 'Last week'],
            ReportDatePreset::ThisMonth => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), 'This month'],
            ReportDatePreset::LastMonth => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth(), 'Last month'],
            ReportDatePreset::ThisQuarter => [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter(), 'This quarter'],
            ReportDatePreset::ThisYear => [$now->copy()->startOfYear(), $now->copy()->endOfYear(), 'This year'],
            ReportDatePreset::Custom => [
                Carbon::parse($from ?? $now->copy()->subDays(29)->toDateString())->startOfDay(),
                Carbon::parse($to ?? $now->toDateString())->endOfDay(),
                'Custom range',
            ],
        };

        if ($preset === ReportDatePreset::Custom) {
            return [
                'from' => $start->toDateString(),
                'to' => $end->toDateString(),
                'label' => $start->format('d M Y').' – '.$end->format('d M Y'),
            ];
        }

        return [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'label' => $label,
        ];
    }

    /**
     * @return array{from: string, to: string}
     */
    public static function previousPeriod(string $from, string $to): array
    {
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();
        $days = max(1, (int) $start->diffInDays($end) + 1);

        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();

        return [
            'from' => $prevStart->toDateString(),
            'to' => $prevEnd->toDateString(),
        ];
    }
}
