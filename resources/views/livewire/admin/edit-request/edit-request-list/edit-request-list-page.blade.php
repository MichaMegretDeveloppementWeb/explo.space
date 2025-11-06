<div class="space-y-4">
    {{-- Header moderne --}}
    <div class="flex flex-col items-start gap-6 md:flex-row md:items-center justify-between mb-12">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Demandes de modifications et signalements</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gérez toutes les demandes de modification, signalements et suggestions de photos
            </p>
        </div>
    </div>

    {{-- Filters component --}}
    <div>
        @livewire('admin.edit-request.edit-request-list.edit-request-list-filters', ['initialFilters' => $initialFilters])
    </div>

    {{-- Table component --}}
    <div>
        @livewire('admin.edit-request.edit-request-list.edit-request-list-table', [
            'initialFilters' => $initialFilters,
            'initialSorting' => $initialSorting,
            'initialPerPage' => $initialPerPage
        ])
    </div>
</div>
