<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-12 flex flex-wrap gap-6 items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Gestion des catégories</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gérez les catégories internes pour organiser les lieux (usage admin uniquement)
            </p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <x-heroicon-o-plus class="h-4 w-4" />
                Nouvelle catégorie
            </a>
        </div>
    </div>

    {{-- Filters Component --}}
    <div>
        @livewire('admin.category.category-list.category-list-filters', ['initialFilters' => $initialFilters])
    </div>

    {{-- Table Component --}}
    <div>
        @livewire('admin.category.category-list.category-list-table', [
            'initialFilters' => $initialFilters,
            'initialSorting' => $initialSorting,
            'initialPerPage' => $initialPerPage,
        ])
    </div>
</div>
