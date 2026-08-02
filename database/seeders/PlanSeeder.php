<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

final class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'badge' => 'Starter',
                'description' => 'Cash up, staff tools and daily reports for small teams.',
                'price_monthly' => 19.99,
                'features' => ['Up to 5 staff', 'Daily cash up', 'Email support'],
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'badge' => 'Growth',
                'description' => 'Everything in Basic plus inventory, payroll and branches.',
                'price_monthly' => 29.99,
                'features' => ['Unlimited staff', 'Inventory', 'Priority support'],
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'badge' => 'Coming soon',
                'description' => 'Placeholder for multi-org enterprise contracts.',
                'price_monthly' => 0,
                'features' => ['Dedicated success', 'Custom SLA', 'Advanced security'],
                'is_featured' => false,
                'is_public' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                array_merge($plan, [
                    'currency' => 'GBP',
                    'billing_interval' => 'monthly',
                    'is_active' => true,
                    'is_public' => $plan['is_public'] ?? true,
                ]),
            );
        }
    }
}
