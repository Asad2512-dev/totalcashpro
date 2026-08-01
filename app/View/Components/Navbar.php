<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Navbar extends Component
{
    /**
     * @return list<array{label: string, href: string}>
     */
    public function links(): array
    {
        return [
            ['label' => 'Features', 'href' => route('home').'#features'],
            ['label' => 'Solutions', 'href' => route('home').'#industries'],
            ['label' => 'Pricing', 'href' => route('home').'#pricing'],
            ['label' => 'About', 'href' => route('about')],
            ['label' => 'Contact', 'href' => route('contact')],
        ];
    }

    public function render(): View
    {
        return view('components.navbar');
    }
}
