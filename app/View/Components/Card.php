<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Card extends Component
{
    public function __construct(
        public bool $hover = true,
        public string $padding = 'p-6 md:p-7',
    ) {}

    public function classes(): string
    {
        $hover = $this->hover
            ? 'transition duration-300 hover:-translate-y-1 hover:shadow-lift'
            : '';

        return trim("rounded-[1.5rem] border border-line bg-snow shadow-[0_1px_0_rgb(15_23_42_/_0.03)] {$this->padding} {$hover}");
    }

    public function render(): View
    {
        return view('components.card');
    }
}
