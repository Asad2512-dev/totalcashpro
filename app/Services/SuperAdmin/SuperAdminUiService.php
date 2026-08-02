<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Organization;

/**
 * Navigation shell for Super Admin. Domain data lives in dedicated services.
 */
final class SuperAdminUiService implements ServiceInterface
{
    /**
     * @return list<array{label: string, items: list<array{label: string, route: string, icon: string}>}>
     */
    public function navigation(): array
    {
        return [
            [
                'label' => 'Overview',
                'items' => [
                    ['label' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'home'],
                    ['label' => 'Analytics', 'route' => 'super-admin.analytics', 'icon' => 'chart'],
                    ['label' => 'System Health', 'route' => 'super-admin.system-health', 'icon' => 'pulse'],
                    ['label' => 'Activity Logs', 'route' => 'super-admin.activity', 'icon' => 'activity'],
                    ['label' => 'Audit Logs', 'route' => 'super-admin.audit-logs', 'icon' => 'shield'],
                ],
            ],
            [
                'label' => 'Customers',
                'items' => [
                    ['label' => 'Businesses', 'route' => 'super-admin.businesses', 'icon' => 'building'],
                    ['label' => 'Business Requests', 'route' => 'super-admin.business-requests', 'icon' => 'inbox'],
                    ['label' => 'Users', 'route' => 'super-admin.users', 'icon' => 'users'],
                    ['label' => 'Contact Messages', 'route' => 'super-admin.contact-messages', 'icon' => 'mail'],
                ],
            ],
            [
                'label' => 'Billing',
                'items' => [
                    ['label' => 'Plans', 'route' => 'super-admin.plans', 'icon' => 'tag'],
                    ['label' => 'Subscriptions', 'route' => 'super-admin.subscriptions', 'icon' => 'repeat'],
                    ['label' => 'Trials', 'route' => 'super-admin.trials', 'icon' => 'clock'],
                    ['label' => 'Coupons', 'route' => 'super-admin.coupons', 'icon' => 'ticket'],
                    ['label' => 'Discounts', 'route' => 'super-admin.discounts', 'icon' => 'percent'],
                    ['label' => 'Payments', 'route' => 'super-admin.payments', 'icon' => 'card'],
                    ['label' => 'Revenue', 'route' => 'super-admin.revenue', 'icon' => 'cash'],
                ],
            ],
            [
                'label' => 'Support',
                'items' => [
                    ['label' => 'Support Tickets', 'route' => 'super-admin.support', 'icon' => 'support'],
                    ['label' => 'Announcements', 'route' => 'super-admin.announcements', 'icon' => 'megaphone'],
                    ['label' => 'Notifications', 'route' => 'super-admin.notifications', 'icon' => 'bell'],
                    ['label' => 'Email Templates', 'route' => 'super-admin.email-templates', 'icon' => 'mail'],
                ],
            ],
            [
                'label' => 'System',
                'items' => [
                    ['label' => 'Settings', 'route' => 'super-admin.settings', 'icon' => 'settings'],
                    ['label' => 'Roles', 'route' => 'super-admin.roles', 'icon' => 'roles'],
                    ['label' => 'Permissions', 'route' => 'super-admin.permissions', 'icon' => 'key'],
                    ['label' => 'Profile', 'route' => 'super-admin.profile', 'icon' => 'user'],
                ],
            ],
        ];
    }

    /**
     * Businesses with nested branches for the sidebar tree.
     *
     * @return list<array{id: int, name: string, url: string, branches: list<array{id: int, name: string, url: string}>}>
     */
    public function businessTree(): array
    {
        return Organization::query()
            ->with(['branches' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get()
            ->map(fn (Organization $organization): array => [
                'id' => $organization->id,
                'name' => $organization->name,
                'url' => route('super-admin.organizations.show', $organization),
                'branches' => $organization->branches->map(fn ($branch): array => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'url' => route('super-admin.branches.edit', $branch),
                ])->values()->all(),
            ])
            ->values()
            ->all();
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
}
