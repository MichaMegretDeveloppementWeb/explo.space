<div class="space-y-4">
    {{-- Header --}}
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900">Liste des administrateurs</h2>
        <p class="mt-1 text-sm text-gray-500">
            Gérez les administrateurs de la plateforme
        </p>
    </div>

    {{-- Filters component --}}
    <div>
        @livewire('admin.settings.admin-list.admin-list-filters')
    </div>

    {{-- Table component --}}
    <div>
        @livewire('admin.settings.admin-list.admin-list-table')
    </div>
</div>
