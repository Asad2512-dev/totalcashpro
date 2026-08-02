<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Navbar extends Component
{
    /**
     * @return list<array{label: string, href: string, match: string}>
     */
    public function links(): array
    {
        return [
            ['label' => 'Features', 'href' => route('home').'#features', 'match' => 'features'],
            ['label' => 'Solutions', 'href' => route('home').'#solutions', 'match' => 'solutions'],
            ['label' => 'Pricing', 'href' => route('home').'#pricing', 'match' => 'pricing'],
            ['label' => 'About', 'href' => route('about'), 'match' => 'about'],
            ['label' => 'Contact', 'href' => route('contact'), 'match' => 'contact'],
        ];
    }

    public function currentMatch(): string
    {
        return match (true) {
            request()->routeIs('about') => 'about',
            request()->routeIs('contact') => 'contact',
            request()->routeIs('request-access*'), request()->routeIs('request-demo') => 'request-demo',
            request()->routeIs('home') => 'home',
            default => '',
        };
    }

    public function isHome(): bool
    {
        return request()->routeIs('home');
    }

    public function render(): View
    {
        return view('components.navbar');
    }
}
