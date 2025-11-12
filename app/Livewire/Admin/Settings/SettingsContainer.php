<?php

namespace App\Livewire\Admin\Settings;

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;

class SettingsContainer extends Component
{
    /**
     * Onglet actif (synchronisé avec l'URL)
     */
    #[Url(as: 'tab', keep: true)]
    public string $activeTab = 'profile';

    /**
     * Onglets disponibles pour tous les admins
     *
     * @var array<int, string>
     */
    protected array $publicTabs = [
        'profile',
        'password',
    ];

    /**
     * Onglets réservés aux super-admins
     *
     * @var array<int, string>
     */
    protected array $restrictedTabs = [
        'create-admin',
        'admin-list',
    ];

    /**
     * Mount du composant
     */
    public function mount(): void
    {
        $this->validateAndSetTab($this->activeTab);
    }

    /**
     * Changer l'onglet actif
     */
    public function setActiveTab(string $tab): void
    {
        $this->validateAndSetTab($tab);
    }

    /**
     * Valider et définir l'onglet actif selon les permissions
     */
    private function validateAndSetTab(string $tab): void
    {
        // Vérifier si l'onglet existe
        $allTabs = array_merge($this->publicTabs, $this->restrictedTabs);
        if (! in_array($tab, $allTabs)) {
            $this->activeTab = 'profile';

            return;
        }

        // Vérifier les permissions pour les onglets restreints
        if (in_array($tab, $this->restrictedTabs)) {
            if (! auth()->user()->can('createUser', User::class)) {
                // Rediriger silencieusement vers profile si pas les droits
                $this->activeTab = 'profile';

                return;
            }
        }

        // Onglet valide et autorisé
        $this->activeTab = $tab;
    }

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.settings.settings-container');
    }
}
