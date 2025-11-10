<div class="space-y-6">
    {{-- Header --}}
    <div class="mb-12 flex flex-wrap gap-6 items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Gestion des tags</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gérez les thématiques utilisées pour catégoriser les lieux spatiaux
            </p>
        </div>
        <div>
            <a href="#"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <x-heroicon-o-plus class="h-4 w-4" />
                Nouveau tag
            </a>
        </div>
    </div>

    {{-- Filters Component --}}
    <div>
        @livewire('admin.tag.tag-list.tag-list-filters', ['initialFilters' => $initialFilters])
    </div>

    {{-- Table Component --}}
    <div>
        @livewire('admin.tag.tag-list.tag-list-table', [
            'initialFilters' => $initialFilters,
            'initialSorting' => $initialSorting,
            'initialPerPage' => $initialPerPage,
        ])
    </div>
</div>
