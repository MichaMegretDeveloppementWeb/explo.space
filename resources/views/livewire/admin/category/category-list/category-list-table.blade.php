<div>
    {{-- En-tête tableau: Statistiques et contrôles --}}
    <div class="mb-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900">{{ $categories->total() }}</span>
                <span class="text-gray-500">{{ $categories->total() > 1 ? 'catégories' : 'catégorie' }}</span>
            </div>

            @if($categories->total() > 0)
                <div class="hidden flex-row items-center gap-2 md:flex">
                    <div class="h-4 w-px bg-gray-200"></div>
                    <div class="text-xs text-gray-500">
                        {{ $categories->firstItem() }}-{{ $categories->lastItem() }} sur {{ $categories->total() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Sélecteur nombre par page --}}
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Lignes par page</span>
            <select id="perPage"
                    wire:model.live="perPage"
                    wire:change="updatePerPage($event.target.value)"
                    class="rounded-lg border-0 py-1.5 pl-3 pr-8 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-600">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    {{-- Table moderne --}}
    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-white">
        {{-- Loading overlay pour le tableau --}}
        <div wire:loading.delay.long>
            <div wire:loading.flex class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        {{-- Cercle extérieur animé --}}
                        <div class="h-12 w-12 rounded-full border-4 border-gray-200"></div>
                        <div class="absolute inset-0 h-12 w-12 animate-spin rounded-full border-4 border-transparent border-t-blue-600 border-r-blue-600"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-900">Chargement des catégories</p>
                        <p class="text-xs text-gray-500 mt-0.5">Veuillez patienter...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        {{-- Colonne Nom (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('name')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Nom</span>
                                @if($sortBy === 'name')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Couleur --}}
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-700">
                            Couleur
                        </th>

                        {{-- Colonne Statut (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('is_active')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Statut</span>
                                @if($sortBy === 'is_active')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Lieux associés (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('places_count')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Lieux associés</span>
                                @if($sortBy === 'places_count')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Date de création (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('created_at')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Créée le</span>
                                @if($sortBy === 'created_at')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Date de modification (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('updated_at')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Modifiée le</span>
                                @if($sortBy === 'updated_at')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @error('load-data')
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-red-100 p-3 mb-4">
                                        <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-red-600" />
                                    </div>
                                    <h3 class="text-sm font-medium text-red-900 mb-1">Erreur de chargement</h3>
                                    <p class="text-xs text-red-600 max-w-sm">
                                        {{ $message }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @else
                        @forelse($categories as $category)
                            <tr class="group relative hover:bg-gray-50/50 transition-colors cursor-pointer" wire:key="{{ $category->id }}">
                                {{-- Nom (avec lien "stretched") --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                               class="text-sm font-medium text-gray-900 truncate hover:text-blue-600 transition-colors after:absolute after:inset-0">
                                                {{ $category->name }}
                                            </a>
                                            <div class="text-xs text-gray-500 truncate mt-0.5">
                                                {{ $category->slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Couleur (pas de lien, juste contenu) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->color)
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 rounded border border-gray-300"
                                                 style="background-color: {{ $category->color }}"></div>
                                            <span class="text-sm text-gray-600">{{ $category->color }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Statut (pas de lien, juste contenu) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-800 border border-green-200">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-50 text-gray-800 border border-gray-200">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Lieux associés (pas de lien, juste contenu) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-map-pin class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                        <span class="text-sm text-gray-600">
                                            {{ $category->places_count }} {{ $category->places_count > 1 ? 'lieux' : 'lieu' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Date de création (pas de lien, juste contenu) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                        <span class="text-xs text-gray-500">
                                            {{ $category->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Date de modification (pas de lien, juste contenu) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                        <span class="text-xs text-gray-500">
                                            {{ $category->updated_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="rounded-full bg-gray-100 p-3 mb-4">
                                            <x-heroicon-o-folder class="h-8 w-8 text-gray-400" />
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune catégorie trouvée</h3>
                                        <p class="text-xs text-gray-500 max-w-sm">
                                            Aucune catégorie ne correspond à vos critères de recherche.
                                            Essayez de modifier vos filtres.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    @enderror
                </tbody>
            </table>
        </div>

        {{-- Pagination moderne --}}
        @if($categories->hasPages())
            <div class="border-t border-gray-200 bg-gray-50/50 px-4 py-3">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
