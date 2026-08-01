<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class FeatureCard extends Component
{
    public function __construct(
        public string $title,
        public string $description,
        public string $icon = 'cash',
        public string $plan = 'basic',
        public string $planLabel = 'Included in Basic',
    ) {}

    public function render(): View
    {
        return view('components.feature-card');
    }
}
