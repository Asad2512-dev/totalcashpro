<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plan;
use App\Services\Billing\FeatureAccessService;
use Illuminate\Database\Seeder;

final class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $access = app(FeatureAccessService::class);

        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'badge' => 'Starter',
                'description' => 'Cash up, attendance and daily reports for single-location teams.',
                'price_monthly' => 19.99,
                'is_featured' => false,
                'sort_order' => 1,
                'bullets' => ['1 branch', 'Up to 5 staff', 'Cash up & attendance', 'Standard reports'],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'badge' => 'Growth',
                'description' => 'Everything in Basic plus inventory, payroll, rota and multi-branch.',
                'price_monthly' => 29.99,
                'is_featured' => true,
                'sort_order' => 2,
                'bullets' => ['Unlimited branches', 'Unlimited staff', 'Inventory & payroll', 'Staff rota & analytics'],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'badge' => 'Scale',
                'description' => 'Full platform access with dedicated commercial terms.',
                'price_monthly' => 0,
                'is_featured' => false,
                'is_public' => false,
                'sort_order' => 3,
                'bullets' => ['Everything in Professional', 'Dedicated success', 'Custom commercial terms'],
            ],
        ];

        foreach ($plans as $plan) {
            $defaults = $access->defaultsForSlug($plan['slug']);
            Plan::query()->updateOrCreate(
                ['slug' => $plan['slug']],
                [
                    'name' => $plan['name'],
                    'badge' => $plan['badge'],
                    'description' => $plan['description'],
                    'price_monthly' => $plan['price_monthly'],
                    'currency' => 'GBP',
                    'billing_interval' => 'monthly',
                    'is_featured' => $plan['is_featured'],
                    'is_active' => true,
                    'is_public' => $plan['is_public'] ?? true,
                    'sort_order' => $plan['sort_order'],
                    'features' => [
                        'bullets' => $plan['bullets'],
                        'entitlements' => array_merge(
                            [
                                'max_branches' => $defaults['max_branches'],
                                'max_staff' => $defaults['max_staff'],
                            ],
                            $defaults['features'],
                        ),
                    ],
                ],
            );
        }
    }
}
