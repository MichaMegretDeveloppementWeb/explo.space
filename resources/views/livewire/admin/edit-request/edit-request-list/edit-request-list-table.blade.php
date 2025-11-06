<div>
    {{-- En-tête tableau: Statistiques et contrôles --}}
    <div class="mb-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900">{{ $editRequests->total() }}</span>
                <span class="text-gray-500">{{ $editRequests->total() > 1 ? 'demandes' : 'demande' }}</span>
            </div>

            @if($editRequests->total() > 0)
                <div class="hidden flex-row items-center gap-2 md:flex">
                    <div class="h-4 w-px bg-gray-200"></div>
                    <div class="text-xs text-gray-500">
                        {{ $editRequests->firstItem() }}-{{ $editRequests->lastItem() }} sur {{ $editRequests->total() }}
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
        <div wire:loading.delay.longer>
            <div wire:loading.flex class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        {{-- Cercle extérieur animé --}}
                        <div class="h-12 w-12 rounded-full border-4 border-gray-200"></div>
                        <div class="absolute inset-0 h-12 w-12 animate-spin rounded-full border-4 border-transparent border-t-blue-600 border-r-blue-600"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-900">Chargement des demandes</p>
                        <p class="text-xs text-gray-500 mt-0.5">Veuillez patienter...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        {{-- Colonne Lieu (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('place')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Lieu</span>
                                @if($sortBy === 'place')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Type (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('type')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Type</span>
                                @if($sortBy === 'type')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Email de contact (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('contact_email')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Contact</span>
                                @if($sortBy === 'contact_email')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Statut (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('status')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Statut</span>
                                @if($sortBy === 'status')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Date de soumission (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('created_at')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Soumis le</span>
                                @if($sortBy === 'created_at')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($editRequests as $request)
                        <tr class="group relative hover:bg-gray-50/50 transition-colors" wire:key="{{ $request->id }}">
                            {{-- Lieu avec lien stretched --}}
                            <td class="px-6 py-4">
                                {{-- TODO: Ajouter le lien vers la page de détail quand elle sera créée --}}
                                {{-- <a href="{{ route('admin.edit-requests.show', ['id' => $request->id]) }}" class="flex items-center gap-3"> --}}
                                <div class="flex items-center gap-3">
                                    {{-- Icône selon le type --}}
                                    <div class="h-10 w-10 flex-shrink-0 rounded-lg {{ $request->type === 'modification' ? 'bg-blue-50' : ($request->type === 'photo_suggestion' ? 'bg-purple-50' : 'bg-orange-50') }} flex items-center justify-center">
                                        @if($request->type === 'modification')
                                            <x-heroicon-o-pencil-square class="h-5 w-5 text-blue-600" />
                                        @elseif($request->type === 'photo_suggestion')
                                            <x-heroicon-o-photo class="h-5 w-5 text-purple-600" />
                                        @else
                                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-orange-600" />
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">
                                            {{ $request->place->translations->first()?->title ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500 truncate mt-0.5">
                                            {{ Str::limit($request->description ?? '', 60) }}
                                        </div>
                                    </div>
                                    {{-- TODO: Décommenter quand la route sera créée --}}
                                    {{-- <span class="absolute inset-0" aria-hidden="true"></span> --}}
                                </div>
                                {{-- </a> --}}
                            </td>

                            {{-- Type (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($request->type === 'modification')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Modification
                                    </span>
                                @elseif($request->type === 'photo_suggestion')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-800 border border-purple-200">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Photo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-50 text-orange-800 border border-orange-200">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Signalement
                                    </span>
                                @endif
                            </td>

                            {{-- Email de contact (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-envelope class="h-4 w-4 text-gray-400 flex-shrink-0" />
                                    <span class="text-sm text-gray-600 truncate">{{ $request->contact_email }}</span>
                                </div>
                            </td>

                            {{-- Statut (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-admin.badge-status :status="$request->status" />
                            </td>

                            {{-- Date de soumission (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                    <span class="text-xs text-gray-500">
                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-gray-100 p-3 mb-4">
                                        <x-heroicon-o-inbox class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune demande trouvée</h3>
                                    <p class="text-xs text-gray-500 max-w-sm">
                                        Aucune demande ne correspond à vos critères de recherche.
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
        @if($editRequests->hasPages())
            <div class="border-t border-gray-200 bg-gray-50/50 px-4 py-3">
                {{ $editRequests->links() }}
            </div>
        @endif
    </div>
</div>
