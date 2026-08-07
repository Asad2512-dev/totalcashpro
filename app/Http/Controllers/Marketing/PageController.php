<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class PageController extends Controller
{
    public function __construct(
        private readonly \App\Services\Marketing\MarketingContentService $marketingContent,
    ) {}

    public function features(): View
    {
        return view('marketing.pages.features', [
            'seo' => [
                'title' => 'Features — Attendance Kiosk, Finance, Payroll & Reports | TotalCashPro',
                'description' => 'Explore TotalCashPro features: attendance kiosk, PIN clock-in, business dashboard, staff panel, finance, inventory, payroll, CRM, HR, reports, 2FA and multi-branch management.',
                'canonical' => route('features'),
            ],
            'features' => $this->marketingContent->features(),
            'featureCategories' => $this->marketingContent->featureCategories(),
        ]);
    }

    public function about(): View
    {
        return view('marketing.about', [
            'seo' => [
                'title' => 'About TotalCashPro — Built for Modern Operations',
                'description' => 'Learn how TotalCashPro helps restaurants and retail businesses manage cash up, attendance, payroll, inventory and multi-branch operations in the cloud.',
            ],
        ]);
    }

    public function privacy(): View
    {
        return view('marketing.privacy', [
            'seo' => [
                'title' => 'Privacy Policy — TotalCashPro',
                'description' => 'Read the TotalCashPro privacy policy and learn how we handle information on our marketing website.',
            ],
        ]);
    }

    public function terms(): View
    {
        return view('marketing.terms', [
            'seo' => [
                'title' => 'Terms of Service — TotalCashPro',
                'description' => 'Review the TotalCashPro terms of service for use of our website and upcoming platform services.',
            ],
        ]);
    }
}
