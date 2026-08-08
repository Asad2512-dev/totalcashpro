<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\User;
use App\Support\Reports\ReportCenterQuery;

final class BusinessInsightService implements ServiceInterface
{
    /**
     * @return list<array{message: string, metric: string, current: float, previous: float, percent: ?float, link: ?string}>
     */
    public function generate(User $user, ReportCenterQuery $query, ReportCenterQuery $prev, array $range): array
    {
        $insights = [];

        $revenue = $query->revenueTotal();
        $prevRevenue = $prev->revenueTotal();
        if ($prevRevenue > 0) {
            $pct = round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1);
            if (abs($pct) >= 1) {
                $insights[] = [
                    'message' => sprintf('Revenue %s %s%% compared with the previous period.', $pct >= 0 ? 'increased' : 'decreased', abs($pct)),
                    'metric' => 'revenue',
                    'current' => $revenue,
                    'previous' => $prevRevenue,
                    'percent' => $pct,
                    'link' => route('business-admin.reports', absolute: false),
                ];
            }
        }

        $spend = $query->supplierSpend();
        $prevSpend = $prev->supplierSpend();
        if ($prevSpend > 0) {
            $pct = round((($spend - $prevSpend) / $prevSpend) * 100, 1);
            if (abs($pct) >= 5) {
                $insights[] = [
                    'message' => sprintf('Supplier spend %s %s%% in %s.', $pct >= 0 ? 'increased' : 'decreased', abs($pct), $range['label'] ?? 'this period'),
                    'metric' => 'supplier_spend',
                    'current' => $spend,
                    'previous' => $prevSpend,
                    'percent' => $pct,
                    'link' => route('business-admin.procurement.dashboard', absolute: false),
                ];
            }
        }

        $branches = $query->branchRevenueComparison();
        if (count($branches) >= 2) {
            $best = $branches[0];
            $weakest = $branches[count($branches) - 1];
            if ($best['revenue'] > 0 && $weakest['revenue'] > 0) {
                $diff = round((($best['revenue'] - $weakest['revenue']) / $weakest['revenue']) * 100, 1);
                $insights[] = [
                    'message' => sprintf('%s generated %s%% higher revenue than %s.', $best['branch'], $diff, $weakest['branch']),
                    'metric' => 'branch_revenue',
                    'current' => $best['revenue'],
                    'previous' => $weakest['revenue'],
                    'percent' => $diff,
                    'link' => route('business-admin.executive.index', absolute: false),
                ];
            }
        }

        if ($query->lowStockCount() > 0) {
            $insights[] = [
                'message' => sprintf('%d inventory items are at or below minimum levels.', $query->lowStockCount()),
                'metric' => 'low_stock',
                'current' => (float) $query->lowStockCount(),
                'previous' => 0,
                'percent' => null,
                'link' => route('business-admin.inventory', absolute: false),
            ];
        }

        return $insights;
    }
}
