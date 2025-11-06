<?php

namespace App\Livewire\Web\Place\PhotoSuggestion;

use App\DTO\Web\Place\PlaceDetailDTO;
use App\Http\Requests\Concerns\HasPhotoValidationRules;
use App\Livewire\Web\Place\PhotoSuggestion\Concerns\ManagesPhotoUpload;
use App\Livewire\Web\Place\PhotoSuggestion\Concerns\ManagesSaving;
use Livewire\Component;
use Livewire\WithFileUploads;

class PhotoSuggestionForm extends Component
{
    use HasPhotoValidationRules;
    use ManagesPhotoUpload;
    use ManagesSaving;
    use WithFileUploads;

    public PlaceDetailDTO $place;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> Photos en cours de validation */
    public array $pendingPhotos = [];

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> Photos validées */
    public array $photos = [];

    public string $contact_email = '';

    public string $recaptcha_token = '';

    public bool $showForm = false;

    public function mount(): void
    {
        // Nothing to initialize
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.web.place.photo-suggestion.photo-suggestion-form');
    }

    /**
     * Remove a photo from the list
     */
    public function removePhoto(int $index): void
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos); // Re-index array
        }
    }

    /**
     * Toggle form visibility
     */
    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;

        // Reset errors when closing form
        if (! $this->showForm) {
            $this->resetValidation();
            $this->reset(['pendingPhotos', 'photos', 'contact_email', 'recaptcha_token']);
        }
    }

    /**
     * Handle reCAPTCHA errors from Alpine.js
     */
    public function handleRecaptchaError(string $errorMessage): void
    {
        $this->addError('recaptcha_token', $errorMessage);
    }
}
