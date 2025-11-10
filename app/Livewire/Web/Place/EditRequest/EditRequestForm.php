<?php

namespace App\Livewire\Web\Place\EditRequest;

use App\DTO\Web\Place\PlaceDetailDTO;
use App\Http\Requests\Web\Place\EditRequestStoreRequest;
use App\Services\Web\Place\EditRequest\EditRequestCreateService;
use Livewire\Attributes\On;
use Livewire\Component;

class EditRequestForm extends Component
{
    public PlaceDetailDTO $place;

    public string $type = 'signalement';

    public string $description = '';

    public string $contact_email = '';

    /** @var array<int, string> */
    public array $selected_fields = [];

    /** @var array<string, mixed> */
    public array $new_values = [
        'title' => '',
        'description' => '',
        'coordinates' => ['lat' => null, 'lng' => null],
        'address' => '',
        'practical_info' => '',
    ];

    public string $recaptcha_token = '';

    public bool $showForm = false;

    /** @var array<string, mixed> */
    public array $current_values = [];

    public function mount(): void
    {

        // Utiliser directement les propriétés du DTO pour affichage "Valeur actuelle"
        $this->current_values = [
            'title' => $this->place->title,
            'description' => $this->place->description,
            'practical_info' => $this->place->practicalInfo ?? '',
            'address' => $this->place->address ?? '',
            'coordinates' => [
                'lat' => $this->place->latitude,
                'lng' => $this->place->longitude,
            ],
        ];

        // Initialiser new_values avec valeurs vides sauf coordonnées
        $this->new_values = [
            'title' => '',
            'description' => '',
            'address' => '',
            'practical_info' => '',
            'coordinates' => $this->current_values['coordinates'], // Garder les coordonnées actuelles
        ];
    }

    /**
     * Hook appelé automatiquement par Livewire quand selected_fields change
     */
    public function updatedSelectedFields(): void
    {
        // Parcourir tous les champs sélectionnés
        foreach ($this->selected_fields as $field) {
            // Si le champ n'est pas 'coordinates', le vider pour forcer une nouvelle saisie
            if ($field !== 'coordinates') {
                $this->new_values[$field] = '';
            }

            // Si c'est 'coordinates', s'assurer que les valeurs actuelles sont présentes
            if ($field === 'coordinates') {
                if (
                    ! isset($this->new_values['coordinates']['lat']) ||
                    ! isset($this->new_values['coordinates']['lng']) ||
                    $this->new_values['coordinates']['lat'] === '' ||
                    $this->new_values['coordinates']['lng'] === ''
                ) {
                    $this->new_values['coordinates'] = $this->current_values['coordinates'];
                }
            }
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.web.place.edit-request.edit-request-form');
    }

    /**
     * Mettre à jour les coordonnées depuis la carte (marker drag)
     */
    #[On('update-coordinates-from-map')]
    public function updateCoordinatesFromMap(float $lat, float $lng): void
    {
        $this->new_values['coordinates']['lat'] = round($lat, 6);
        $this->new_values['coordinates']['lng'] = round($lng, 6);
    }

    /**
     * Soumettre le formulaire
     */
    public function submit(?string $recaptchaToken = null): void
    {
        // Assigner le token reCAPTCHA reçu depuis Alpine.js
        if ($recaptchaToken) {
            $this->recaptcha_token = $recaptchaToken;
        }

        $formRequest = new EditRequestStoreRequest;
        $formRequest->merge([
            'type' => $this->type,
            'selected_fields' => $this->selected_fields,
        ]);
        // Valider avec les règles et messages du FormRequest
        try {
            $validated = $this->validate($formRequest->rules(), $formRequest->messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        try {

            // Appeler le Service avec le DTO (plus besoin de recharger depuis la base)
            $service = app(EditRequestCreateService::class);
            $service->createEditRequest($validated, $this->place);

            // Flash message + reset
            session()->flash('success', __('web/pages/place-show.edit_request.success'));

            // Reset du formulaire
            $this->reset(['type', 'description', 'contact_email', 'selected_fields', 'recaptcha_token']);
            $this->new_values = $this->current_values;
            $this->showForm = false;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create EditRequest', [
                'error' => $e->getMessage(),
                'place_id' => $this->place->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->addError('submit', __('web/pages/place-show.edit_request.error'));
        }
    }

    public function openSignalementForm(): void
    {
        $this->type = 'signalement';
        $this->showForm = true;
        $this->resetValidation();
    }

    public function openModificationForm(): void
    {
        $this->type = 'modification';
        $this->showForm = true;
        $this->resetValidation();
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;

        // Reset errors when closing form
        if (! $this->showForm) {
            $this->resetValidation();
            $this->reset(['selected_fields', 'description', 'contact_email', 'recaptcha_token']);
            $this->new_values = $this->current_values;
        }
    }

    /**
     * Gérer les erreurs reCAPTCHA depuis Alpine.js
     */
    public function handleRecaptchaError(string $errorMessage): void
    {
        $this->addError('recaptcha_token', $errorMessage);
    }
}
