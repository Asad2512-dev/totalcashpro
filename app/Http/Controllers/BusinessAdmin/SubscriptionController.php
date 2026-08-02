<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $organization = $request->user()->organization;
        $organization?->load('currentSubscription.plan');
        $subscription = $organization?->currentSubscription;

        return view('business-admin.subscription.index', [
            'organization' => $organization,
            'subscription' => $subscription,
            'plan' => $subscription?->plan,
        ]);
    }
}
