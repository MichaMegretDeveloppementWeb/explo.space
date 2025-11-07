<?php

namespace App\Livewire\Admin\Place\Store;

use App\Contracts\Repositories\Admin\Category\CategorySelectionRepositoryInterface;
use App\Contracts\Repositories\Admin\Tag\TagSelectionRepositoryInterface;
use App\Livewire\Admin\Place\Store\Concerns\ManagesLoadData;
use App\Livewire\Admin\Place\Store\Concerns\ManagesLocation;
use App\Livewire\Admin\Place\Store\Concerns\ManagesPhotos;
use App\Livewire\Admin\Place\Store\Concerns\ManagesRelations;
use App\Livewire\Admin\Place\Store\Concerns\ManagesSaving;
use App\Livewire\Admin\Place\Store\Concerns\ManagesTranslations;
use App\Models\Place;
use App\Models\PlaceRequest;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlaceStoreForm extends Component
{
    use ManagesLoadData;
    use ManagesLocation;
    use ManagesPhotos;
    use ManagesRelations;
    use ManagesSaving;
    use ManagesTranslations;
    use WithFileUploads;

    // Mode
    public string $mode = 'create'; // 'create' or 'edit'

    public ?int $placeId = null;

    public ?int $placeRequestId = null;

    public ?int $editRequestId = null;

    // EditRequest-specific properties
    /** @var array<int, string> */
    public array $highlightedFields = []; // Champs sélectionnés à mettre en évidence

    /** @var array<string, mixed> */
    public array $oldValues = []; // Valeurs originales pour comparaison visuelle

    public ?float $originalLatitude = null; // Pour double marker GPS

    public ?float $originalLongitude = null; // Pour double marker GPS

    /** @var array<int, array{id: int, url: string, medium_url: string, source: string}> */
    public array $editRequestPhotos = []; // Photos from EditRequest (edit mode only)

    /** @var array<int, string> */
    public array $selectedFields = []; // Champs sélectionnés par admin depuis EditRequestDetail

    /** @var array<int, int> */
    public array $selectedPhotos = []; // Photos sélectionnées par admin depuis EditRequestDetail

    // Active translation tab (synchronized with Alpine via @entangle)
    public string $activeTranslationTab = 'fr';

    // Base place data
    public ?float $latitude = 0.0;

    public ?float $longitude = 0.0;

    public ?string $address = null;

    public bool $is_featured = false;

    // Translations (indexed by locale)
    /** @var array<string, array{title: string, slug: string, description: string, practical_info: string, status: string}> */
    public array $translations = [];

    // Translation management (used by ManagesTranslations trait)
    /** @var array<string, mixed> */
    public array $pendingTranslations = [];

    public bool $showTranslationConfirmation = false;

    public string $translationSourceLocale = '';

    public string $translationTargetLocale = '';

    /** @var array<int, string> */
    public array $fieldsToOverwrite = [];

    /** @var array<int, string> */
    public array $selectedFieldsToOverwrite = [];

    public bool $hasEmptyFieldsToTranslate = false;

    public ?string $firstErrorTab = null;

    // PlaceRequest translation detection
    public ?string $detectedLanguage = null;

    public ?string $detectedLanguageName = null;

    public bool $isTranslatedFromSource = false;

    public bool $showSpecialTranslateButton = false;

    // EditRequest per-field language detection
    /** @var array<string, string> Map field -> detected_language (ex: ['title' => 'en', 'description' => 'fr']) */
    public array $fieldLanguages = [];

    /** @var array<string, string> Map field -> original language for translated fields (ex: ['practical_info' => 'pl']) */
    public array $fieldTranslatedFrom = [];

    // Relations
    /** @var array<int, int> */
    public array $categoryIds = [];

    /** @var array<int, int> */
    public array $tagIds = [];

    // Photos
    /** @var array<int, mixed> */
    public array $photos = []; // Current upload input (TemporaryUploadedFile array)

    /** @var array<int, mixed> */
    public array $pendingPhotos = []; // Validated photos waiting to be saved

    /** @var array<int, mixed> */
    public array $existingPhotos = []; // For edit mode

    /** @var array<int, array{id: int, url: string, medium_url: string, source: string}> */
    public array $placeRequestPhotos = []; // Photos from PlaceRequest (create mode only)

    /** @var array<int, int> */
    public array $deletedPhotoIds = [];

    /** @var array<int, int> */
    public array $photoOrder = [];

    public ?int $mainPhotoId = null;

    // Address management
    public string $queryAddress = '';

    public ?string $placeAddress = null;

    /** @var array<int, mixed> */
    public array $suggestions = [];

    public bool $showSuggestions = false;

    // Available options for selects
    /** @var Collection<int, \App\Models\Category> */
    public Collection $availableCategories;

    /** @var Collection<int, \App\Models\Tag> */
    public Collection $availableTags;

    public function mount(
        ?int $placeId,
        ?int $placeRequestId,
        ?int $editRequestId,
        CategorySelectionRepositoryInterface $categoryRepository,
        TagSelectionRepositoryInterface $tagRepository
    ): void {
        $this->placeId = $placeId;
        $this->placeRequestId = $placeRequestId;
        $this->editRequestId = $editRequestId;

        // Load available options with all translations
        $this->availableCategories = $categoryRepository->getAll();
        $this->availableTags = $tagRepository->getAll();

        // Initialize translations structure
        $supportedLocales = config('locales.supported', ['fr', 'en']);
        foreach ($supportedLocales as $locale) {
            $this->translations[$locale] = [
                'title' => '',
                'slug' => '',
                'description' => '',
                'practical_info' => '',
                'status' => 'published',
            ];
        }

        if ($placeId) {
            // Edit mode
            $this->mode = 'edit';
            $this->loadPlaceForEdit($placeId);

            // Apply EditRequest overlay if present
            if ($editRequestId) {
                // Récupérer selectedFields et selectedPhotos depuis la query string
                $selectedFields = request()->query('selected_fields', []);
                $selectedPhotos = request()->query('selected_photos', []);

                $this->loadFromEditRequest($editRequestId, $selectedFields, $selectedPhotos);
            }
        } elseif ($placeRequestId) {
            // Create from PlaceRequest
            $this->loadFromPlaceRequest($placeRequestId);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.place.store.place-store-form');
    }
}
