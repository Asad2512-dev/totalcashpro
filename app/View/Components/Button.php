<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Button extends Component
{
    public function __construct(
        public string $href = '#',
        public string $variant = 'primary',
        public string $size = 'md',
        public bool $external = false,
    ) {}

    public function classes(): string
    {
        $base = 'btn-ripple inline-flex items-center justify-center gap-2 font-semibold tracking-tight transition duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-royal-600 focus-visible:ring-offset-2';

        $variants = [
            'primary' => 'bg-royal-600 text-white shadow-lift hover:-translate-y-0.5 hover:bg-royal-700',
            'secondary' => 'border border-royal-600 bg-snow text-royal-600 hover:-translate-y-0.5 hover:bg-royal-50',
            'outline' => 'border border-line bg-snow text-ink hover:border-royal-600/40 hover:bg-snow',
            'ghost' => 'text-navy-800 hover:bg-navy-50 hover:text-navy-900',
            'navy' => 'bg-navy-900 text-white shadow-lift hover:-translate-y-0.5 hover:bg-navy-800',
            'white' => 'bg-snow text-navy-900 shadow-lift hover:-translate-y-0.5 hover:bg-paper',
        ];

        $sizes = [
            'sm' => 'rounded-xl px-4 py-2 text-sm',
            'md' => 'rounded-2xl px-5 py-3 text-sm',
            'lg' => 'rounded-2xl px-6 py-3.5 text-base',
        ];

        return implode(' ', [
            $base,
            $variants[$this->variant] ?? $variants['primary'],
            $sizes[$this->size] ?? $sizes['md'],
        ]);
    }

    public function render(): View
    {
        return view('components.button');
    }
}
