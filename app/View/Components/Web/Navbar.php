<?php

namespace App\View\Components\Web;

use Illuminate\View\Component;
use Illuminate\View\View;

class Navbar extends Component
{
    /**
     * Get the navigation links for the navbar.
     *
     * @return array<int, array<string, string>>
     */
    public function getNavigationLinks(): array
    {
        return [
            [
                'key' => 'home',
                'label' => __('web/components/navbar.navigation.home'),
                'route' => 'home',
                'url' => localRoute('home'),
            ],
            [
                'key' => 'features',
                'label' => __('web/components/navbar.navigation.features'),
                'route' => 'features',
                'url' => '#', // À remplacer plus tard par la vraie route
            ],
            [
                'key' => 'explore',
                'label' => __('web/components/navbar.navigation.explore'),
                'route' => 'explore',
                'url' => localRoute('explore'), // À remplacer plus tard par la vraie route
            ],
            [
                'key' => 'community',
                'label' => __('web/components/navbar.navigation.community'),
                'route' => 'community',
                'url' => '#', // À remplacer plus tard par la vraie route
            ],
            [
                'key' => 'about',
                'label' => __('web/components/navbar.navigation.about'),
                'route' => 'about',
                'url' => '#', // À remplacer plus tard par la vraie route
            ],
        ];
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
