<?php

declare(strict_types=1);

namespace App\Services\Staff;

use App\Contracts\ServiceInterface;
use App\Enums\PlanFeature;
use App\Services\Billing\FeatureAccessService;

final class StaffUiService implements ServiceInterface
{
    public function __construct(private readonly FeatureAccessService $features) {}

    /**
     * @return list<array{label: string, items: list<array{label: string, route: string, icon: string, feature?: string}>}>
     */
    public function navigation(): array
    {
        $user = auth()->user();
        $groups = [
            [
                'label' => 'Today',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'staff.dashboard', 'icon' => 'home'],
                    ['label' => 'Clock In', 'route' => 'staff.clock', 'icon' => 'clock', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Attendance', 'route' => 'staff.attendance', 'icon' => 'activity', 'feature' => PlanFeature::Attendance->value],
                ],
            ],
            [
                'label' => 'Work',
                'items' => [
                    ['label' => 'Cash Up', 'route' => 'staff.cash-up', 'icon' => 'cash', 'feature' => PlanFeature::CashUp->value],
                    ['label' => 'My Shift', 'route' => 'staff.shift', 'icon' => 'repeat', 'feature' => PlanFeature::Rota->value],
                    ['label' => 'Shift Swaps', 'route' => 'staff.shift-swap', 'icon' => 'repeat', 'feature' => PlanFeature::Rota->value],
                    ['label' => 'Weekly Hours', 'route' => 'staff.hours', 'icon' => 'activity', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Availability', 'route' => 'staff.availability', 'icon' => 'clock', 'feature' => PlanFeature::Attendance->value],
                    ['label' => 'Leave', 'route' => 'staff.leave', 'icon' => 'user', 'feature' => PlanFeature::Attendance->value],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['label' => 'Notifications', 'route' => 'staff.notifications', 'icon' => 'bell'],
                    ['label' => 'Profile', 'route' => 'staff.profile', 'icon' => 'user'],
                ],
            ],
        ];

        if ($user === null) {
            return $groups;
        }

        foreach ($groups as &$group) {
            $group['items'] = array_values(array_filter(
                $group['items'],
                function (array $item) use ($user): bool {
                    if (! isset($item['feature'])) {
                        return true;
                    }

                    return $this->features->can($user, $item['feature']);
                },
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
                $links[] = ['label' => $item['label'], 'route' => $item['route']];
            }
        }

        return $links;
    }
}
