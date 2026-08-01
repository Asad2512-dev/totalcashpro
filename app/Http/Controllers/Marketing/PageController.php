<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class PageController extends Controller
{
    public function about(): View
    {
        return view('marketing.about', [
            'seo' => [
                'title' => 'About TotalCashPro — Built for Modern Operations',
                'description' => 'Learn how TotalCashPro helps restaurants and retail businesses unify cash up, attendance, payroll, inventory, and multi-branch operations.',
            ],
        ]);
    }

    public function contact(): View
    {
        return view('marketing.contact', [
            'seo' => [
                'title' => 'Contact TotalCashPro',
                'description' => 'Get in touch with the TotalCashPro team to book a demo or ask about restaurant and retail operations software.',
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
