<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Testimonial extends Component
{
    public function __construct(
        public string $quote,
        public string $name,
        public string $role,
        public string $business,
    ) {}

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = array_map(
            static fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)),
            array_slice($parts, 0, 2),
        );

        return implode('', $letters);
    }

    public function render(): View
    {
        return view('components.testimonial');
    }
}
