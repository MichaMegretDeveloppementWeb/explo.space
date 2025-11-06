<div>
    {{-- En-tête tableau: Statistiques et contrôles --}}
    <div class="mb-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900">{{ $places->total() }}</span>
                <span class="text-gray-500">{{ $places->total() > 1 ? 'lieux' : 'lieu' }}</span>
            </div>

            @if($places->total() > 0)
                <div class="hidden flex-row items-center gap-2 md:flex">
                    <div class="h-4 w-px bg-gray-200"></div>
                    <div class="text-xs text-gray-500">
                        {{ $places->firstItem() }}-{{ $places->lastItem() }} sur {{ $places->total() }}
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
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    {{-- Table moderne --}}
    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-white">
        {{-- Loading overlay pour le tableau --}}
        <div wire:loading.delay.longer>
            <div wire:loading.flex class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        {{-- Cercle extérieur animé --}}
                        <div class="h-12 w-12 rounded-full border-4 border-gray-200"></div>
                        <div class="absolute inset-0 h-12 w-12 animate-spin rounded-full border-4 border-transparent border-t-blue-600 border-r-blue-600"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-900">Chargement des lieux</p>
                        <p class="text-xs text-gray-500 mt-0.5">Veuillez patienter...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        {{-- Colonne Titre (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('title')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Lieu</span>
                                @if($sortBy === 'title')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Tags (non triable) --}}
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-700">
                            Thématiques
                        </th>

                        {{-- Colonne Mise à l'affiche (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('is_featured')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Affiche</span>
                                @if($sortBy === 'is_featured')
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
                                <span>Créé</span>
                                @if($sortBy === 'created_at')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Dernière modification (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('updated_at')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Modifié</span>
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
                    @forelse($places as $place)
                        @php
                            // Sécuriser l'accès à la traduction dans la bonne locale
                            $translation = $place->translations->firstWhere('locale', $locale)
                                ?? $place->translations->first();
                        @endphp
                        <tr class="group relative hover:bg-gray-50/50 transition-colors" wire:key="{{ $place->id }}">
                            {{-- Titre avec lien stretched --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.places.show', ['id' => $place->id]) }}" class="flex items-center gap-3">
                                    @if($place->photos->isNotEmpty())
                                        <div class="h-11 w-11 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 ring-1 ring-gray-200">
                                            <img class="h-full w-full object-cover"
                                                 src="{{ $place->photos->first()->url ?? '#' }}"
                                                 alt="{{ $translation->title ?? 'Photo' }}">
                                        </div>
                                    @else
                                        <div class="h-11 w-11 flex-shrink-0 rounded-lg bg-gray-100 ring-1 ring-gray-200 flex items-center justify-center">
                                            <x-heroicon-o-photo class="h-5 w-5 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">
                                            {{ $translation->title ?? 'Sans titre' }}
                                        </div>
                                        <div class="text-xs text-gray-500 truncate mt-0.5">
                                            {{ Str::limit($translation->description ?? '', 60) }}
                                        </div>
                                    </div>
                                    {{-- Span étiré qui couvre toute la ligne --}}
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                </a>
                            </td>

                            {{-- Tags (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($place->tags->take(3) as $tag)
                                        @php
                                            // Sécuriser l'accès à la traduction du tag dans la bonne locale
                                            $tagTranslation = $tag->translations->firstWhere('locale', $locale)
                                                ?? $tag->translations->first();
                                        @endphp
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                                            {{ $tagTranslation->name ?? $tag->id }}
                                        </span>
                                    @endforeach
                                    @if($place->tags->count() > 3)
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-500 ring-1 ring-inset ring-gray-200">
                                            +{{ $place->tags->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Mise à l'affiche (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($place->is_featured)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        <x-heroicon-s-star class="h-3 w-3" />
                                        Oui
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Date de création (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-gray-500">
                                    {{ $place->created_at->format('d/m/Y') }}
                                </span>
                            </td>

                            {{-- Dernière modification (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-gray-500">
                                    {{ $place->updated_at->format('d/m/Y') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-gray-100 p-3 mb-4">
                                        <x-heroicon-o-map-pin class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucun lieu trouvé</h3>
                                    <p class="text-xs text-gray-500 max-w-sm">
                                        Aucun lieu ne correspond à vos critères de recherche.
                                        Essayez de modifier vos filtres.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination moderne --}}
        @if($places->hasPages())
            <div class="border-t border-gray-200 bg-gray-50/50 px-4 py-3">
                {{ $places->links() }}
            </div>
        @endif
    </div>
</div>
