<div class="space-y-4">
    {{-- Header moderne --}}
    <div class="flex flex-col items-start gap-6 md:flex-row md:items-center justify-between mb-12">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Propositions de lieux</h1>
            <p class="mt-1 text-sm text-gray-500">
                Gérez les propositions de lieux soumises par les visiteurs
            </p>
        </div>
    </div>

    {{-- Filters component --}}
    <div>
        @livewire('admin.place-request.place-request-list.place-request-list-filters', ['initialFilters' => $initialFilters])
    </div>

    {{-- Table component --}}
    <div>
        @livewire('admin.place-request.place-request-list.place-request-list-table', [
            'initialFilters' => $initialFilters,
            'initialSorting' => $initialSorting,
            'initialPerPage' => $initialPerPage
        ])
    </div>
</div>
