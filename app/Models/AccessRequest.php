<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccessRequestStatus;
use App\Enums\SubscriptionPlan;
use Illuminate\Database\Eloquent\Model;

final class AccessRequest extends Model
{
    protected $fillable = [
        'business_name',
        'owner_name',
        'email',
        'phone',
        'business_address',
        'country',
        'business_type',
        'number_of_employees',
        'selected_plan',
        'additional_notes',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'selected_plan' => SubscriptionPlan::class,
            'status' => AccessRequestStatus::class,
        ];
    }
}
