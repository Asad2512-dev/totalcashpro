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
                    ['label' => 'Reports', 'route' => 'business-admin.reports', 'icon' => 'chart', 'feature' => PlanFeature::Reports->value],
                ],
            ],
            [
                'label' => 'Operations',
                'items' => [
                    ['label' => 'Cash Up', 'route' => 'business-admin.cash-up', 'icon' => 'cash', 'feature' => PlanFeature::CashUp->value],
                    ['label' => 'Cash History', 'route' => 'business-admin.cash-history', 'icon' => 'activity', 'feature' => PlanFeature::CashUp->value],
                    ['label' => 'Inventory', 'route' => 'business-admin.inventory', 'icon' => 'tag', 'feature' => PlanFeature::Inventory->value],
                    ['label' => 'Inventory History', 'route' => 'business-admin.inventory-history', 'icon' => 'pulse', 'feature' => PlanFeature::Inventory->value],
                    ['label' => 'Suppliers', 'route' => 'business-admin.suppliers', 'icon' => 'building', 'feature' => PlanFeature::Suppliers->value],
                ],
            ],
            [
                'label' => 'People',
                'items' => [
                    ['label' => 'Staff', 'route' => 'business-admin.staff', 'icon' => 'users'],
                    ['label' => 'Clock In', 'route' => 'business-admin.clock-in', 'icon' => 'clock', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Attendance', 'route' => 'business-admin.attendance', 'icon' => 'activity', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Staff Rota', 'route' => 'business-admin.rota', 'icon' => 'repeat', 'feature' => PlanFeature::Rota->value],
                    ['label' => 'Payroll', 'route' => 'business-admin.payroll', 'icon' => 'card', 'feature' => PlanFeature::Payroll->value],
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
