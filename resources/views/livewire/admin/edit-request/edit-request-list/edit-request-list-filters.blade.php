{{-- Design moderne compact inspiré Stripe, Linear, Vercel --}}
<div>
    {{-- Header avec bouton reset --}}
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-sm font-medium text-gray-700">Filtres</h2>
        @if($search !== '' || $type !== '' || $status !== '')
            <button type="button"
                    wire:click="resetFilters"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all">
                <x-heroicon-o-arrow-path class="h-3.5 w-3.5" />
                Réinitialiser les filtres
            </button>
        @endif
    </div>

    {{-- Card de filtres - Layout horizontal compact --}}
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="p-4">
            {{-- Ligne unique de filtres avec séparateurs verticaux --}}
            <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-0 lg:divide-x lg:divide-gray-200">

                {{-- Section 1: Recherche (flex-1 pour étalement) --}}
                <div class="lg:pr-4 flex-1">
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
                               placeholder="Nom du lieu ou email..."
                               class="block w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                    </div>
                </div>

                {{-- Section 2: Type (width fixe) --}}
                <div class="lg:px-4 lg:min-w-[180px] w-full lg:w-auto">
                    <label for="type" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Type
                    </label>
                    <select id="type"
                            wire:model.live="type"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                        <option value="">Tous les types</option>
                        <option value="modification">Modification</option>
                        <option value="signalement">Signalement</option>
                        <option value="photo_suggestion">Photo</option>
                    </select>
                </div>

                {{-- Section 3: Statut (width fixe) --}}
                <div class="lg:pl-4 lg:min-w-[180px] w-full lg:w-auto">
                    <label for="status" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Statut
                    </label>
                    <select id="status"
                            wire:model.live="status"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                        <option value="">Tous les statuts</option>
                        <option value="submitted">Envoyé</option>
                        <option value="pending">En attente</option>
                        <option value="accepted">Accepté</option>
                        <option value="refused">Refusé</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
