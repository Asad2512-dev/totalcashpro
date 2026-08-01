<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class PricingCard extends Component
{
    /**
     * @param  list<string>  $features
     * @param  list<string>  $assurances
     */
    public function __construct(
        public string $name,
        public string $price,
        public string $badge,
        public string $description,
        public array $features,
        public string $cta,
        public array $assurances = [],
        public string $period = '/month',
        public ?string $popularBadge = null,
        public bool $featured = false,
        public string $ctaHref = '#',
    ) {}

    public function render(): View
    {
        return view('components.pricing-card');
    }
}
