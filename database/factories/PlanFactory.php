<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
final class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'badge' => 'Plan',
            'description' => fake()->sentence(),
            'price_monthly' => 19.99,
            'currency' => 'GBP',
            'billing_interval' => 'monthly',
            'features' => ['Feature A', 'Feature B'],
            'is_featured' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 1,
        ];
    }
}
