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
        $base = 'btn-ripple inline-flex items-center justify-center gap-2 font-semibold tracking-tight transition duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 focus-visible:ring-offset-2';

        $variants = [
            'primary' => 'bg-primary-600 text-white shadow-lift hover:-translate-y-0.5 hover:bg-primary-700',
            'secondary' => 'border border-primary-600 bg-snow text-primary-700 hover:-translate-y-0.5 hover:bg-primary-50',
            'outline' => 'border border-line bg-snow text-ink hover:border-primary-600/40 hover:bg-snow',
            'ghost' => 'text-gray-700 hover:bg-gray-100 hover:text-gray-900',
            'navy' => 'bg-gray-900 text-white shadow-lift hover:-translate-y-0.5 hover:bg-gray-800',
            'white' => 'bg-snow text-gray-900 shadow-lift hover:-translate-y-0.5 hover:bg-paper',
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
