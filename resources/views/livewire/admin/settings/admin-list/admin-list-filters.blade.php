{{-- Filtres pour la liste des administrateurs --}}
<div>
    {{-- Header avec bouton reset --}}
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-sm font-medium text-gray-700">Filtres</h2>
        @if($search !== '')
            <button type="button"
                    wire:click="resetFilters"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all">
                <x-heroicon-o-arrow-path class="h-3.5 w-3.5" />
                Réinitialiser les filtres
            </button>
        @endif
    </div>

    {{-- Card de filtres --}}
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="p-4">
            {{-- Champ de recherche --}}
            <div>
                <label for="search" class="block text-xs font-medium text-gray-700 mb-1.5">
                    Rechercher
                </label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <x-heroicon-o-magnifying-glass class="h-4 w-4 text-gray-400" />
                    </div>
                    <input type="search"
                           id="search"
                           wire:model.live.debounce.500ms="search"
                           placeholder="Nom ou adresse email..."
                           class="block w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                </div>
            </div>
        </div>
    </div>
</div>
