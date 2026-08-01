<?php

declare(strict_types=1);

namespace App\Http\Requests\Marketing;

use App\Enums\SubscriptionPlan;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

final class StoreAccessRequestRequest extends BaseFormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:40'],
            'business_address' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'business_type' => ['required', 'string', 'max:100'],
            'number_of_employees' => ['required', 'string', 'max:50'],
            'selected_plan' => ['required', Rule::enum(SubscriptionPlan::class)],
            'additional_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'selected_plan.required' => 'Please select a plan.',
        ];
    }
}
