<?php

namespace App\View\Components\Web;

use Illuminate\View\Component;
use Illuminate\View\View;

class Navbar extends Component
{
    /**
     * Get the navigation links for the navbar.
     *
     * @return array<int, array<string, string|bool>>
     */
    public function getNavigationLinks(): array
    {
        $links = [
            [
                'key' => 'home',
                'label' => __('web/components/navbar.navigation.home'),
                'route' => 'home',
                'url' => localRoute('home'),
            ],
            [
                'key' => 'explore',
                'label' => __('web/components/navbar.navigation.explore'),
                'route' => 'explore',
                'url' => localRoute('explore'),
            ],
            [
                'key' => 'about',
                'label' => __('web/components/navbar.navigation.about'),
                'route' => 'about',
                'url' => localRoute('about'),
            ],
            [
                'key' => 'contact',
                'label' => __('web/components/navbar.navigation.contact'),
                'route' => 'contact',
                'url' => localRoute('contact'),
            ],
        ];

        // Ajouter le flag 'active' pour chaque lien
        return array_map(function ($link) {
            $link['active'] = $this->isLinkActive($link['route']);

            return $link;
        }, $links);
    }

    /**
     * Check if a navigation link is active.
     */
    private function isLinkActive(string $routePattern): bool
    {
        // Vérifier si la route actuelle correspond au pattern
        // Supporte les patterns avec wildcard (ex: 'explore.*')
        return request()->routeIs($routePattern.'.'.app()->getLocale())
            || request()->routeIs($routePattern.'.*');
    }

    /**
     * Get the primary action for the navbar.
     *
     * @return array<string, string>
     */
    public function getPrimaryAction(): array
    {
        return [
            'key' => 'suggest_place',
            'label' => __('web/components/navbar.actions.suggest_place'),
            'route' => 'place_requests.create',
            'url' => localRoute('place_requests.create'),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.web.navbar', [
            'navigationLinks' => $this->getNavigationLinks(),
            'primaryAction' => $this->getPrimaryAction(),
        ]);
    }
}
