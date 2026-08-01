<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class SectionTitle extends Component
{
    public function __construct(
        public ?string $eyebrow = null,
        public string $title = '',
        public ?string $subtitle = null,
        public string $align = 'center',
    ) {}

    public function alignClasses(): string
    {
        return match ($this->align) {
            'left' => 'text-left items-start',
            default => 'text-center items-center mx-auto',
        };
    }

    public function render(): View
    {
        return view('components.section-title');
    }
}
