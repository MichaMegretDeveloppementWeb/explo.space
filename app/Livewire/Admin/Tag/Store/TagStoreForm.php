<?php

namespace App\Livewire\Admin\Tag\Store;

use App\Livewire\Admin\Tag\Store\Concerns\ManagesLoadData;
use App\Livewire\Admin\Tag\Store\Concerns\ManagesSaving;
use App\Livewire\Admin\Tag\Store\Concerns\ManagesTranslations;
use Livewire\Component;

class TagStoreForm extends Component
{
    use ManagesLoadData;
    use ManagesSaving;
    use ManagesTranslations;

    // Mode
    public string $mode = 'create'; // 'create' or 'edit'

    public ?int $tagId = null;

    // Tag model (loaded once, used in multiple places)
    public ?\App\Models\Tag $tag = null;

    // Active translation tab (synchronized with Alpine via @entangle)
    public string $activeTranslationTab = 'fr';

    // Base tag data
    public string $color = '#3B82F6'; // Default blue color

    public bool $is_active = true;

    // Translations (indexed by locale)
    /** @var array<string, array{name: string, slug: string, description: ?string}> */
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

    public function mount(?int $tagId): void
    {
        $this->tagId = $tagId;
        $this->mode = $tagId ? 'edit' : 'create';

        // Initialize translations structure
        $this->initializeTranslations();

        // Load tag data if editing
        if ($this->mode === 'edit' && $this->tagId) {
            $this->loadTag($this->tagId);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.tag.store.tag-store-form');
    }

    /**
     * Initialize empty translations structure for all supported locales
     */
    private function initializeTranslations(): void
    {
        $supportedLocales = config('locales.supported', ['fr', 'en']);

        foreach ($supportedLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'slug' => '',
                'description' => null,
            ];
        }
    }
}
