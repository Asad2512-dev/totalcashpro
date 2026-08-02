<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class BrandLogo extends Component
{
    public function __construct(
        public string $height = 'h-9',
    ) {}

    public function render(): View
    {
        return view('components.brand-logo');
    }
}
