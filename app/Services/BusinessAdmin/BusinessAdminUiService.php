<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\PlanFeature;
use App\Models\Branch;
use App\Models\User;
use App\Services\Billing\FeatureAccessService;
use Illuminate\Support\Collection;

/**
 * Navigation shell for Business Admin. Domain data lives in dedicated services.
 */
final class BusinessAdminUiService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FeatureAccessService $features,
    ) {}

    /**
     * @return list<array{label: string, items: list<array{label: string, route: string, icon: string, feature?: string}>}>
     */
    public function navigation(): array
    {
        $groups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'business-admin.dashboard', 'icon' => 'home'],
                    ['label' => 'Executive', 'route' => 'business-admin.executive.index', 'icon' => 'chart', 'feature' => PlanFeature::Reports->value],
                    ['label' => 'Reports Center', 'route' => 'business-admin.reports', 'icon' => 'chart', 'feature' => PlanFeature::Reports->value],
                ],
            ],
            [
                'label' => 'Operations',
                'items' => [
                    ['label' => 'Cash Up', 'route' => 'business-admin.cash-up', 'icon' => 'cash', 'feature' => PlanFeature::CashUp->value],
                    ['label' => 'Tills', 'route' => 'business-admin.cash-drawers', 'icon' => 'cash', 'feature' => PlanFeature::CashUp->value],
                    ['label' => 'Cash History', 'route' => 'business-admin.cash-history', 'icon' => 'activity', 'feature' => PlanFeature::CashUp->value],
                    ['label' => 'Inventory', 'route' => 'business-admin.inventory', 'icon' => 'tag', 'feature' => PlanFeature::Inventory->value],
                    ['label' => 'Weekly Stocktake', 'route' => 'business-admin.stocktake.index', 'icon' => 'tag', 'feature' => PlanFeature::Inventory->value],
                    ['label' => 'Riders', 'route' => 'business-admin.riders.index', 'icon' => 'user', 'feature' => PlanFeature::Inventory->value],
                    ['label' => 'Inventory History', 'route' => 'business-admin.inventory-history', 'icon' => 'pulse', 'feature' => PlanFeature::Inventory->value],
                ],
            ],
            [
                'label' => 'Finance',
                'items' => [
                    ['label' => 'Finance', 'route' => 'business-admin.finance.dashboard', 'icon' => 'cash', 'feature' => PlanFeature::Accounting->value],
                    ['label' => 'Procurement', 'route' => 'business-admin.procurement.dashboard', 'icon' => 'chart', 'feature' => PlanFeature::Suppliers->value],
                    ['label' => 'Suppliers', 'route' => 'business-admin.suppliers', 'icon' => 'building', 'feature' => PlanFeature::Suppliers->value],
                    ['label' => 'Receiving', 'route' => 'business-admin.receiving.index', 'icon' => 'package', 'feature' => PlanFeature::Suppliers->value],
                    ['label' => 'Purchase Orders', 'route' => 'business-admin.purchase-orders.index', 'icon' => 'tag', 'feature' => PlanFeature::Suppliers->value],
                ],
            ],
            [
                'label' => 'People',
                'items' => [
                    ['label' => 'Staff', 'route' => 'business-admin.staff', 'icon' => 'users'],
                    ['label' => 'HR', 'route' => 'business-admin.hr', 'icon' => 'user'],
                    ['label' => 'Customers', 'route' => 'business-admin.crm', 'icon' => 'users'],
                    ['label' => 'Kiosk', 'route' => 'business-admin.kiosk.settings', 'icon' => 'clock', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Attendance', 'route' => 'business-admin.attendance', 'icon' => 'activity', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Staff Rota', 'route' => 'business-admin.rota', 'icon' => 'repeat', 'feature' => PlanFeature::Rota->value],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['label' => 'Branches', 'route' => 'business-admin.branches', 'icon' => 'building'],
                    ['label' => 'Subscription', 'route' => 'business-admin.subscription', 'icon' => 'ticket'],
                    ['label' => 'Notifications', 'route' => 'business-admin.notifications', 'icon' => 'bell'],
                    ['label' => 'Settings', 'route' => 'business-admin.settings', 'icon' => 'settings'],
                    ['label' => 'Profile', 'route' => 'business-admin.profile', 'icon' => 'user'],
                ],
            ],
        ];

        $user = auth()->user();
        if ($user === null) {
            return $groups;
        }

        foreach ($groups as &$group) {
            $group['items'] = array_values(array_filter(
                $group['items'],
                fn (array $item) => ! isset($item['feature']) || $this->features->can($user, $item['feature']),
            ));
        }

        return array_values(array_filter($groups, fn (array $g) => $g['items'] !== []));
    }

    /**
     * @return list<array{label: string, route: string}>
     */
    public function commandLinks(): array
    {
        $links = [];

        foreach ($this->navigation() as $group) {
            foreach ($group['items'] as $item) {
                $links[] = [
                    'label' => $item['label'],
                    'route' => $item['route'],
                ];
            }
        }

        return $links;
    }

    /**
     * Primary bottom navigation for mobile (max 5 items).
     *
     * @return list<array{label: string, route?: string, icon: string, active: list<string>, feature?: string, more?: bool}>
     */
    public function mobileNavigation(): array
    {
        $items = [
            ['label' => 'Home', 'route' => 'business-admin.dashboard', 'icon' => 'home', 'active' => ['dashboard']],
            ['label' => 'Cash Up', 'route' => 'business-admin.cash-up', 'icon' => 'cash', 'feature' => PlanFeature::CashUp->value, 'active' => ['cash-up', 'cash-history']],
            ['label' => 'People', 'route' => 'business-admin.staff', 'icon' => 'users', 'active' => ['staff', 'hr', 'crm', 'attendance', 'rota', 'kiosk', 'kiosks', 'clock-in', 'payroll']],
            ['label' => 'Reports', 'route' => 'business-admin.reports', 'icon' => 'chart', 'feature' => PlanFeature::Reports->value, 'active' => ['reports', 'accounting', 'finance', 'suppliers', 'purchase-orders']],
            ['label' => 'More', 'icon' => 'more', 'active' => ['branches', 'subscription', 'notifications', 'settings', 'profile', 'inventory', 'inventory-history'], 'more' => true],
        ];

        $user = auth()->user();
        if ($user === null) {
            return $items;
        }

        return array_values(array_filter(
            $items,
            fn (array $item) => ! isset($item['feature']) || $this->features->can($user, $item['feature']),
        ));
    }

    /**
     * Secondary navigation shown in the mobile "More" sheet.
     *
     * @return list<array{label: string, items: list<array{label: string, route: string, icon: string}>}>
     */
    public function mobileMoreNavigation(): array
    {
        $primaryRoutes = collect($this->mobileNavigation())
            ->filter(fn (array $item) => ! ($item['more'] ?? false))
            ->pluck('route')
            ->filter()
            ->all();

        $more = [];

        foreach ($this->navigation() as $group) {
            $items = array_values(array_filter(
                $group['items'],
                fn (array $item) => ! in_array($item['route'], $primaryRoutes, true),
            ));

            if ($items !== []) {
                $more[] = [
                    'label' => $group['label'],
                    'items' => $items,
                ];
            }
        }

        return $more;
    }

    /**
     * @return Collection<int, Branch>
     */
    public function branchesFor(User $user): Collection
    {
        return $this->branchContext->resolveBranches($user);
    }

    public function selectedBranchId(User $user): ?int
    {
        return $this->branchContext->currentBranchId($user);
    }
}
