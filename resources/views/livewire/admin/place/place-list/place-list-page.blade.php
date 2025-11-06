<div class="space-y-4">
    {{-- Header moderne --}}
    <div class="flex flex-col items-start gap-6 md:flex-row md:items-center justify-between mb-12">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Lieux</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gérez tous les lieux de la plateforme
            </p>
        </div>

        {{-- Action button: Create new place --}}
        <div>
            <a href="{{ route('admin.places.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <x-heroicon-o-plus class="h-4 w-4" />
                Nouveau lieu
            </a>
        </div>
    </div>

    {{-- Filters component --}}
    <div>
        @livewire('admin.place.place-list.place-list-filters', ['initialFilters' => $initialFilters])
    </div>

    {{-- Table component --}}
    <div>
        @livewire('admin.place.place-list.place-list-table', [
            'initialFilters' => $initialFilters,
            'initialSorting' => $initialSorting,
            'initialPerPage' => $initialPerPage
        ])
    </div>
</div>
