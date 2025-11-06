<?php

namespace App\Livewire\Admin\Place;

use App\Models\Place;
use Livewire\Component;

class PlaceTranslationViewer extends Component
{
    public Place $place;

    public string $selectedLocale = 'fr';

    public function mount(Place $place): void
    {
        $this->place = $place;

        // Sélectionner 'fr' par défaut si disponible, sinon la première traduction
        $frTranslation = $this->place->translations->firstWhere('locale', 'fr');
        if ($frTranslation) {
            $this->selectedLocale = 'fr';
        } else {
            $firstTranslation = $this->place->translations->first();
            if ($firstTranslation) {
                $this->selectedLocale = $firstTranslation->locale;
            }
        }
    }

    public function selectLocale(string $locale): void
    {
        $this->selectedLocale = $locale;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $selectedTranslation = $this->place->translations->firstWhere('locale', $this->selectedLocale);

        // Trier les traductions avec 'fr' en premier, puis par ordre alphabétique
        $sortedTranslations = $this->place->translations->sortBy(function ($translation) {
            return $translation->locale === 'fr' ? '0' : $translation->locale;
        })->values();

        return view('livewire.admin.place.place-translation-viewer', [
            'translations' => $sortedTranslations,
            'selectedTranslation' => $selectedTranslation,
        ]);
    }
}
