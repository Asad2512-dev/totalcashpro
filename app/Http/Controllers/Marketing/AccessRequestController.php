<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Actions\Marketing\StoreAccessRequestAction;
use App\Enums\SubscriptionPlan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreAccessRequestRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AccessRequestController extends Controller
{
    public function create(Request $request): View
    {
        $plan = $request->query('plan');
        $selectedPlan = SubscriptionPlan::tryFrom(is_string($plan) ? $plan : '')?->value;

        return view('marketing.request-access', [
            'selectedPlan' => $selectedPlan,
            'plans' => SubscriptionPlan::cases(),
            'businessTypes' => [
                'Restaurant',
                'Café',
                'Takeaway',
                'Food Truck',
                'Retail Store',
                'Pharmacy',
                'Salon',
                'Bakery',
                'Convenience Store',
                'Other',
            ],
            'employeeRanges' => [
                '1-5',
                '6-15',
                '16-50',
                '51-100',
                '100+',
            ],
            'seo' => [
                'title' => 'Request Access — TotalCashPro',
                'description' => 'Request Basic or Professional access to TotalCashPro. Our team reviews every request and creates your account manually.',
            ],
        ]);
    }

    public function store(
        StoreAccessRequestRequest $request,
        StoreAccessRequestAction $action,
    ): RedirectResponse {
        $action->execute($request->validated());

        return redirect()
            ->route('request-access.thanks')
            ->with('status', 'Your request has been submitted.');
    }

    public function thanks(): View
    {
        return view('marketing.request-access-thanks', [
            'seo' => [
                'title' => 'Request Received — TotalCashPro',
                'description' => 'Your TotalCashPro access request has been received. Our team will review it and contact you within 24 hours.',
            ],
        ]);
    }
}
