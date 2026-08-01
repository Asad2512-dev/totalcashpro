<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Badge extends Component
{
    public function __construct(
        public string $tone = 'royal',
    ) {}

    public function classes(): string
    {
        $tones = [
            'royal' => 'bg-royal-50 text-royal-700 ring-royal-600/15',
            'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
            'sky' => 'bg-sky-50 text-navy-800 ring-sky-400/30',
            'navy' => 'bg-navy-50 text-navy-800 ring-navy-900/10',
            'soft' => 'bg-snow text-mute ring-line',
        ];

        return 'inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ring-1 ring-inset '.($tones[$this->tone] ?? $tones['royal']);
    }

    public function render(): View
    {
        return view('components.badge');
    }
}
