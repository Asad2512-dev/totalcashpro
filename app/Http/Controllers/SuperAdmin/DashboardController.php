<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\DashboardAnalyticsService;
use Illuminate\Contracts\View\View;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardAnalyticsService $analytics,
    ) {}

    public function __invoke(): View
    {
        return view('admin.dashboard.index', [
            'stats' => $this->analytics->stats(),
            'revenueBars' => $this->analytics->monthlyRevenueBars(),
            'growthBars' => $this->analytics->subscriptionGrowthBars(),
            'recentBusinesses' => $this->analytics->recentBusinesses(),
            'recentActivity' => $this->analytics->recentActivity(),
            'latestPayments' => $this->analytics->latestPayments(),
            'recentTickets' => $this->analytics->recentTickets(),
        ]);
    }
}
