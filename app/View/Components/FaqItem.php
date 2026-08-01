<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class FaqItem extends Component
{
    public function __construct(
        public string $question,
        public string $answer,
        public int $index = 0,
    ) {}

    public function render(): View
    {
        return view('components.faq-item');
    }
}
