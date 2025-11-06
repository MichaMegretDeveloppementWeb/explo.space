<?php

namespace App\Livewire\Web\Place\Index;

use App\DTO\Web\Place\PlacePreviewDTO;
use App\Exceptions\Web\Place\PreviewModal\PlaceNotFoundException;
use App\Exceptions\Web\Place\PreviewModal\PlaceTranslationNotFoundException;
use App\Services\Web\Place\PreviewModal\PlacePreviewService;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Livewire\Attributes\On;
use Livewire\Component;

class PlacePreviewModal extends Component
{
    /**
     * Indique si la modale est ouverte
     */
    public bool $isOpen = false;

    /**
     * Données du lieu à afficher dans la modale
     * Public : sérialisé/désérialisé par Livewire via Wireable
     */
    public ?PlacePreviewDTO $place = null;

    /**
     * Message d'erreur utilisateur
     */
    public ?string $errorMessage = null;

    /**
     * Message d'erreur technique (visible uniquement en mode développement)
     */
    public ?string $technicalError = null;

    /**
     * Écoute l'événement marker-clicked et charge les données du lieu
     *
     * Gestion des exceptions :
     * - InvalidArgumentException : ID invalide
     * - PlaceNotFoundException : Lieu introuvable
     * - PlaceTranslationNotFoundException : Traduction manquante
     * - Exception générique : Erreur inattendue
     *
     * @param  int  $placeId  ID du lieu cliqué
     * @param  PlacePreviewService  $placePreviewService  Service injecté via méthode
     */
    #[On('marker-clicked')]
    public function loadPlace(int $placeId, PlacePreviewService $placePreviewService): void
    {
        // Réinitialiser l'état
        $this->errorMessage = null;
        $this->technicalError = null;
        $this->place = null;

        try {
            // Récupérer les données via le service
            $this->place = $placePreviewService->getPlacePreviewById($placeId);

            // Ouvrir la modale
            $this->isOpen = true;

        } catch (InvalidArgumentException $e) {
            // ID invalide
            $this->errorMessage = __('web/pages/explore.place_preview.error_invalid_id');
            $this->handleError($e, $placeId);

        } catch (PlaceNotFoundException $e) {
            // Lieu introuvable en base
            $this->errorMessage = __('web/pages/explore.place_preview.error_not_found');
            $this->handleError($e, $placeId);

        } catch (PlaceTranslationNotFoundException $e) {
            // Traduction manquante pour la locale active
            $this->errorMessage = __('web/pages/explore.place_preview.error_translation_missing');
            $this->handleError($e, $placeId);

        } catch (\Exception $e) { // @phpstan-ignore-line
            // Erreur inattendue : catch générique intentionnel pour robustesse
            $this->errorMessage = __('web/pages/explore.place_preview.error_loading');
            $this->handleError($e, $placeId);
        }

    }

    /**
     * Gère l'erreur : log + affichage technique en dev + ouverture modale
     */
    private function handleError(\Exception $exception, int $placeId): void
    {
        // Logger l'erreur
        Log::error('Error loading place preview modal', [
            'place_id' => $placeId,
            'exception_type' => get_class($exception),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // En mode développement, afficher le message technique
        if (config('app.debug')) {
            $this->technicalError = get_class($exception).': '.$exception->getMessage();
        }

        // Ouvrir la modale pour afficher l'erreur
        $this->isOpen = true;
    }

    /**
     * Ferme la modale
     */
    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->place = null;
        $this->errorMessage = null;
    }

    /**
     * Render du composant
     * $place est automatiquement disponible dans la vue (propriété publique)
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.web.place.index.place-preview-modal');
    }
}
