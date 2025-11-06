<?php

namespace App\Livewire\Web\Place\PlaceRequest;

use App\Exceptions\Photo\PhotoProcessingException;
use App\Exceptions\Photo\PhotoValidationException;
use App\Exceptions\Photo\UnexpectedPhotoException;
use App\Livewire\Web\Place\PlaceRequest\Concerns\ManagesPlaceRequestLocation;
use App\Livewire\Web\Place\PlaceRequest\Concerns\ManagesPlaceRequestPhotos;
use App\Rules\RecaptchaRule;
use App\Services\Web\Place\Request\PlaceRequestCreateService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class PlaceRequestForm extends Component
{
    use ManagesPlaceRequestLocation;
    use ManagesPlaceRequestPhotos;
    use WithFileUploads;

    // ==================== Contact ====================
    public string $contact_email = '';

    // ==================== Place Information ====================
    public string $title = '';

    public string $description = '';

    public string $practical_info = '';

    // ==================== Location ====================
    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?string $address = null;

    public string $queryAddress = '';

    public ?string $placeAddress = null;

    /** @var array<int, mixed> */
    public array $suggestions = [];

    public bool $showSuggestions = false;

    // ==================== Photos ====================
    /** @var array<int, TemporaryUploadedFile> */
    public array $photos = [];

    /** @var array<int, TemporaryUploadedFile> */
    public array $pendingPhotos = [];

    // ==================== reCAPTCHA ====================
    public string $recaptcha_token = '';

    /**
     * Render du composant
     */
    public function render(): View
    {
        /*if ($this->getErrorBag()){
            dump($this->getErrorBag());
        }*/
        return view('livewire.web.place.place-request.place-request-form', [
            'photoConfig' => $this->getPhotoValidationConfig(),
        ]);
    }

    /**
     * Règles de validation
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            // Contact (obligatoire)
            'contact_email' => ['required', 'email', 'max:255'],

            // Place information (titre obligatoire)
            'title' => ['required', 'string', 'min:7', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'practical_info' => ['nullable', 'string', 'max:2000'],

            // Location (optionnelle)
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],

            // Photos (optionnelles)
            ...$this->getPhotoValidationRules('photos'),

            // reCAPTCHA (obligatoire)
            'recaptcha_token' => ['required', new RecaptchaRule],
        ];
    }

    /**
     * Messages de validation personnalisés
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            // Contact
            'contact_email.required' => __('web/pages/place-request.validation.email_required'),
            'contact_email.email' => __('web/pages/place-request.validation.email_valid'),
            'contact_email.max' => __('web/pages/place-request.validation.email_max'),

            // Place information
            'title.required' => __('web/pages/place-request.validation.title_required'),
            'title.min' => __('web/pages/place-request.validation.title_min'),
            'title.max' => __('web/pages/place-request.validation.title_max'),
            'description.max' => __('web/pages/place-request.validation.description_max'),
            'practical_info.max' => __('web/pages/place-request.validation.practical_info_max'),

            // Location
            'latitude.numeric' => __('web/pages/place-request.validation.latitude_numeric'),
            'latitude.between' => __('web/pages/place-request.validation.latitude_between'),
            'longitude.numeric' => __('web/pages/place-request.validation.longitude_numeric'),
            'longitude.between' => __('web/pages/place-request.validation.longitude_between'),
            'address.max' => __('web/pages/place-request.validation.address_max'),

            // Photos
            ...$this->getPhotoValidationMessages('photos'),

            // reCAPTCHA
            'recaptcha_token.required' => __('web/pages/place-request.validation.recaptcha_required'),
        ];
    }

    /**
     * Soumettre le formulaire
     *
     * @param  string  $recaptchaToken  Token reCAPTCHA v3 obtenu par Alpine.js/JavaScript
     */
    public function submit(string $recaptchaToken = ''): void
    {

        $this->resetErrorBag();

        // Si un token est passé en paramètre, l'utiliser
        if (! empty($recaptchaToken)) {
            $this->recaptcha_token = $recaptchaToken;
        }

        // Valider le formulaire et récupérer les données validées
        try {
            $validated = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Dispatch event to trigger scroll to first error
            $this->dispatch('scroll-to-validation-error');
            throw $e;
        }

        try {
            // Passer les données validées au service
            $placeRequest = app(PlaceRequestCreateService::class)->create($validated);

            // Flash message de succès
            session()->flash('success', __('web/pages/place-request.messages.success'));

            // Redirection vers la homepage
            $this->redirect(route('home.'.app()->getLocale()), navigate: true);

        } catch (PhotoValidationException|PhotoProcessingException|UnexpectedPhotoException $e) {
            // Exceptions photo : traduction basée sur le type d'erreur
            $errorType = $e->getErrorType();
            $translatedMessage = $this->getTranslatedErrorMessage($errorType);

            $this->addError('photos', $translatedMessage);

        } catch (\Throwable $e) {
            $message = __('errors/place-request.general');
            if (config('app.debug')) {
                $message .= ' '.$e->getMessage();
            }
            // Erreur générique
            $this->addError('submit', $message);
        }
    }

    /**
     * Obtenir le message d'erreur traduit depuis le type d'erreur
     *
     * @param  string  $errorType  Type d'erreur (ex: 'photo.size_limit')
     * @return string Message traduit dans la langue courante
     */
    private function getTranslatedErrorMessage(string $errorType): string
    {
        $translationKey = "errors/place-request.{$errorType}";

        // Vérifier si la traduction existe
        if (__($translationKey) !== $translationKey) {
            return __($translationKey);
        }

        // Fallback vers message générique si type inconnu
        return __('errors/place-request.general');
    }

    /**
     * Gérer une erreur reCAPTCHA
     */
    public function handleRecaptchaError(string $message): void
    {
        $this->addError('recaptcha_token', $message);
    }
}
