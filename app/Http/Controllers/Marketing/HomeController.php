<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\Marketing\MarketingContentService;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __construct(
        private readonly MarketingContentService $marketingContent,
    ) {}

    public function __invoke(): View
    {
        return view('marketing.home', [
            'heroStats' => $this->marketingContent->heroStats(),
            'trustedIndustries' => $this->marketingContent->trustedIndustries(),
            'features' => $this->marketingContent->features(),
            'workflow' => $this->marketingContent->workflow(),
            'industries' => $this->marketingContent->industries(),
            'whyChoose' => $this->marketingContent->whyChoose(),
            'testimonials' => $this->marketingContent->testimonials(),
            'pricingPlans' => $this->marketingContent->pricingPlans(),
            'pricingComparison' => $this->marketingContent->pricingComparison(),
            'faqs' => $this->marketingContent->faqs(),
            'employeeWorkflow' => $this->marketingContent->employeeWorkflow(),
        ]);
    }
}
