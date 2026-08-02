<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Organization;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
final class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'amount' => 29.99,
            'currency' => 'GBP',
            'provider' => 'manual',
            'provider_reference' => 'PAY-'.fake()->unique()->numerify('#####'),
            'status' => PaymentStatus::Paid,
            'method' => 'card',
            'paid_at' => now(),
        ];
    }
}
