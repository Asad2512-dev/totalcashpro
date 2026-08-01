<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class PricingCard extends Component
{
    /**
     * @param  list<string>  $features
     */
    public function __construct(
        public string $name,
        public string $price,
        public string $badge,
        public string $description,
        public array $features,
        public string $cta,
        public string $note = 'No subscriptions. No recurring charges.',
        public string $ctaHref = '#buy',
    ) {}

    public function render(): View
    {
        return view('components.pricing-card');
    }
}
