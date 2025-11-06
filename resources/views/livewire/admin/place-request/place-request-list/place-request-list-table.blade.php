<div>
    {{-- En-tête tableau: Statistiques et contrôles --}}
    <div class="mb-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3">
        <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900">{{ $placeRequests->total() }}</span>
                <span class="text-gray-500">{{ $placeRequests->total() > 1 ? 'propositions' : 'proposition' }}</span>
            </div>

            @if($placeRequests->total() > 0)
                <div class="hidden flex-row items-center gap-2 md:flex">
                    <div class="h-4 w-px bg-gray-200"></div>
                    <div class="text-xs text-gray-500">
                        {{ $placeRequests->firstItem() }}-{{ $placeRequests->lastItem() }} sur {{ $placeRequests->total() }}
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
                        <p class="text-sm font-medium text-gray-900">Chargement des propositions</p>
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
                                <span>Lieu proposé</span>
                                @if($sortBy === 'title')
                                    <x-heroicon-s-chevron-up class="h-3.5 w-3.5 {{ $sortDirection === 'asc' ? 'text-blue-600' : 'rotate-180 text-blue-600' }}" />
                                @else
                                    <x-heroicon-o-arrows-up-down class="h-3.5 w-3.5 text-gray-400 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>

                        {{-- Colonne Email contact --}}
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-700">
                            Contact
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

                        {{-- Colonne Date (triable) --}}
                        <th scope="col"
                            wire:click="sortByColumn('created_at')"
                            class="group cursor-pointer px-6 py-3.5 text-left text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-100/50">
                            <div class="flex items-center gap-1.5">
                                <span>Date de soumission</span>
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
                    @forelse($placeRequests as $request)
                        <tr class="group relative hover:bg-gray-50/50 transition-colors" wire:key="{{ $request->id }}">
                            {{-- Titre avec lien stretched --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.place-requests.show', ['id' => $request->id]) }}" class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50">
                                            <x-heroicon-o-map-pin class="h-5 w-5 text-blue-600" />
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $request->title }}
                                        </p>
                                        @if($request->address)
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ $request->address }}
                                            </p>
                                        @endif
                                    </div>
                                    {{-- Span étiré qui couvre toute la ligne --}}
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                </a>
                            </td>

                            {{-- Email contact (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-envelope class="h-4 w-4 text-gray-400" />
                                    <span class="text-sm text-gray-600">{{ $request->contact_email }}</span>
                                </div>
                            </td>

                            {{-- Statut (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-admin.badge-status :status="$request->status" />
                            </td>

                            {{-- Date (pas de lien, juste contenu) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                    <span>{{ $request->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                        <x-heroicon-o-inbox class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Aucune proposition trouvée</p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if(empty($status))
                                                Aucune proposition n'a été soumise pour le moment.
                                            @else
                                                Aucune proposition avec les statuts sélectionnés.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($placeRequests->hasPages())
            <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                {{ $placeRequests->links() }}
            </div>
        @endif
    </div>
</div>
