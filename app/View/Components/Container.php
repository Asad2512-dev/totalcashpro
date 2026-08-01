<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Container extends Component
{
    public function __construct(
        public string $width = 'default',
    ) {}

    public function classes(): string
    {
        $widths = [
            'narrow' => 'max-w-3xl',
            'default' => 'max-w-7xl',
            'wide' => 'max-w-[88rem]',
        ];

        return ($widths[$this->width] ?? $widths['default']).' mx-auto w-full px-5 sm:px-6 lg:px-8';
    }

    public function render(): View
    {
        return view('components.container');
    }
}
