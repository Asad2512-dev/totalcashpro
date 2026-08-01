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
            [
                'label' => 'Features',
                'href' => route('home').'#features',
                'match' => 'home#features',
            ],
            [
                'label' => 'How Access Works',
                'href' => route('home').'#workflow',
                'match' => 'home#workflow',
            ],
            [
                'label' => 'Pricing',
                'href' => route('home').'#pricing',
                'match' => 'home#pricing',
            ],
            [
                'label' => 'About',
                'href' => route('about'),
                'match' => 'about',
            ],
            [
                'label' => 'Contact',
                'href' => route('contact'),
                'match' => 'contact',
            ],
        ];
    }

    public function currentMatch(): string
    {
        if (request()->routeIs('about')) {
            return 'about';
        }

        if (request()->routeIs('contact')) {
            return 'contact';
        }

        if (request()->routeIs('request-access*')) {
            return 'request-access';
        }

        if (request()->routeIs('home')) {
            return 'home';
        }

        return '';
    }

    public function render(): View
    {
        return view('components.navbar');
    }
}
