<?php

namespace App\Livewire\Admin\Place\PlaceList;

use App\Contracts\Repositories\Admin\Category\CategorySelectionRepositoryInterface;
use App\Services\Admin\Tag\TagSelectionService;
use Livewire\Component;

class PlaceListFilters extends Component
{
    /**
     * Filtres locaux
     */
    public string $search = '';

    /** @var array<int, string> */
    public array $tags = [];

    /** @var array<int, int> */
    public array $categories = [];

    public string $locale = 'fr';

    /**
     * Recherche de tags (autosuggestion)
     */
    public string $tagSearchInput = '';

    /**
     * Liste complète des tags disponibles (chargée une seule fois)
     * Utilisée comme source pour le filtrage côté PHP
     *
     * @var array<int, array{slug: string, name: string}>
     */
    public array $availableTags = [];

    /**
     * Tags filtrés affichés dans les suggestions
     *
     * @var array<int, array{slug: string, name: string}>
     */
    public array $tagSuggestions = [];

    /**
     * Recherche de catégories (autosuggestion)
     */
    public string $categorySearchInput = '';

    /**
     * Liste complète des catégories disponibles (chargée une seule fois)
     * Utilisée comme source pour le filtrage côté PHP
     *
     * @var array<int, array{id: int, name: string}>
     */
    public array $availableCategories = [];

    /**
     * Catégories filtrées affichées dans les suggestions
     *
     * @var array<int, array{id: int, name: string}>
     */
    public array $categorySuggestions = [];

    /**
     * Initialiser depuis les props du parent
     *
     * @param  array{search: string, tags: array<int, string>, categories: array<int, int>, locale: string}  $initialFilters
     */
    public function mount(array $initialFilters): void
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->tags = $initialFilters['tags'] ?? [];
        $this->categories = $initialFilters['categories'] ?? [];
        $this->locale = $initialFilters['locale'] ?? 'fr';

        // Charger tous les tags et catégories disponibles UNE SEULE FOIS
        $this->loadAvailableTags();
        $this->loadAvailableCategories();
    }

    /**
     * Render du composant
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.place.place-list.place-list-filters');
    }

    public function updatedSearch(): void
    {
        $this->applyFilters();
    }

    public function setLocale(string $locale): void
    {
        // Valider la locale
        $newLocale = in_array($locale, ['fr', 'en']) ? $locale : 'fr';

        // Si la locale change
        if ($newLocale !== $this->locale) {
            $oldLocale = $this->locale;
            $this->locale = $newLocale;

            // Recharger les tags disponibles dans la nouvelle locale
            $this->loadAvailableTags();

            // Traduire les tags sélectionnés si nécessaire
            if (! empty($this->tags)) {
                $this->translateSelectedTags($oldLocale, $newLocale);
            }
        }

        $this->applyFilters();
    }

    /**
     * Traduire les tags sélectionnés d'une locale à une autre
     * Les tags sans traduction sont supprimés
     */
    private function translateSelectedTags(string $fromLocale, string $toLocale): void
    {
        if (empty($this->tags)) {
            return;
        }

        $tagService = app(TagSelectionService::class);

        // Traduire les slugs
        $translatedSlugs = $tagService->translateTagSlugs($this->tags, $fromLocale, $toLocale);

        // Mettre à jour la sélection avec les slugs traduits
        $this->tags = $translatedSlugs;
    }

    /**
     * Charger tous les tags disponibles depuis la DB
     * Appelé uniquement au mount() et au changement de locale
     */
    private function loadAvailableTags(): void
    {
        $tagService = app(TagSelectionService::class);
        $this->availableTags = $tagService->getAvailableTagsForLocale($this->locale);

        // Initialiser les suggestions avec tous les tags disponibles
        $this->filterTagSuggestions();
    }

    /**
     * Filtrer les suggestions côté PHP (instantané, pas de requête DB)
     * Appelé à chaque modification du champ de recherche
     */
    public function updatedTagSearchInput(): void
    {
        $this->filterTagSuggestions();
    }

    /**
     * Filtrer les tags disponibles selon la recherche
     * Filtrage en PHP sur le tableau $availableTags, pas de requête DB
     */
    private function filterTagSuggestions(): void
    {
        $query = trim($this->tagSearchInput);

        if (strlen($query) === 0) {
            // Aucune recherche : afficher tous les tags disponibles
            $this->tagSuggestions = $this->availableTags;

            return;
        }

        // Recherche case-insensitive sur le nom
        $this->tagSuggestions = array_values(array_filter(
            $this->availableTags,
            fn ($tag) => stripos($tag['name'], $query) !== false
        ));

        // Optionnel : limiter à 100 résultats pour performance d'affichage
        if (count($this->tagSuggestions) > 100) {
            $this->tagSuggestions = array_slice($this->tagSuggestions, 0, 100);
        }
    }

    /**
     * Ajouter un tag sélectionné
     */
    public function addTag(string $slug): void
    {
        if (! in_array($slug, $this->tags, true)) {
            $this->tags[] = $slug;
            $this->applyFilters();
        }

        $this->tagSearchInput = '';
        $this->tagSuggestions = $this->availableTags;
    }

    /**
     * Retirer un tag
     */
    public function removeTag(string $slug): void
    {
        $this->tags = array_values(array_filter($this->tags, fn ($tag) => $tag !== $slug));
        $this->applyFilters();
    }

    /**
     * Charger toutes les catégories disponibles depuis la DB
     * Appelé uniquement au mount() (les catégories n'ont pas de traductions)
     */
    private function loadAvailableCategories(): void
    {
        $categoryRepository = app(CategorySelectionRepositoryInterface::class);
        $categories = $categoryRepository->getAll();

        $this->availableCategories = $categories->map(fn ($category) => [
            'id' => $category->id,
            'name' => $category->name,
        ])->toArray();

        // Initialiser les suggestions avec toutes les catégories disponibles
        $this->filterCategorySuggestions();
    }

    /**
     * Filtrer les suggestions de catégories côté PHP (instantané, pas de requête DB)
     * Appelé à chaque modification du champ de recherche
     */
    public function updatedCategorySearchInput(): void
    {
        $this->filterCategorySuggestions();
    }

    /**
     * Filtrer les catégories disponibles selon la recherche
     * Filtrage en PHP sur le tableau $availableCategories, pas de requête DB
     */
    private function filterCategorySuggestions(): void
    {
        $query = trim($this->categorySearchInput);

        if (strlen($query) === 0) {
            // Aucune recherche : afficher toutes les catégories disponibles
            $this->categorySuggestions = $this->availableCategories;

            return;
        }

        // Recherche case-insensitive sur le nom
        $this->categorySuggestions = array_values(array_filter(
            $this->availableCategories,
            fn ($category) => stripos($category['name'], $query) !== false
        ));

        // Optionnel : limiter à 100 résultats pour performance d'affichage
        if (count($this->categorySuggestions) > 100) {
            $this->categorySuggestions = array_slice($this->categorySuggestions, 0, 100);
        }
    }

    /**
     * Récupérer les noms des catégories sélectionnées
     * Computed property pour la vue
     *
     * @return array<int, string> [id => name]
     */
    public function getSelectedCategoryNamesProperty(): array
    {
        $names = [];
        foreach ($this->categories as $categoryId) {
            foreach ($this->availableCategories as $category) {
                if ($category['id'] === $categoryId) {
                    $names[$categoryId] = $category['name'];
                    break;
                }
            }
        }

        return $names;
    }

    /**
     * Ajouter une catégorie sélectionnée
     */
    public function addCategory(int $categoryId): void
    {
        if (! in_array($categoryId, $this->categories, true)) {
            $this->categories[] = $categoryId;
            $this->applyFilters();
        }

        $this->categorySearchInput = '';
        $this->categorySuggestions = $this->availableCategories;
    }

    /**
     * Retirer une catégorie
     */
    public function removeCategory(int $categoryId): void
    {
        $this->categories = array_values(array_filter($this->categories, fn ($id) => $id !== $categoryId));
        $this->applyFilters();
    }

    /**
     * Appliquer les filtres (déclenche événement)
     */
    public function applyFilters(): void
    {
        // Dispatch vers parent pour sync URL
        $this->dispatch('filters:updated',
            search: $this->search,
            tags: $this->tags,
            categories: $this->categories,
            locale: $this->locale
        );
    }

    /**
     * Réinitialiser tous les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->tags = [];
        $this->categories = [];
        $this->locale = 'fr';
        $this->tagSearchInput = '';
        $this->categorySearchInput = '';
        $this->tagSuggestions = $this->availableTags;
        $this->categorySuggestions = $this->availableCategories;

        $this->applyFilters();
    }
}
