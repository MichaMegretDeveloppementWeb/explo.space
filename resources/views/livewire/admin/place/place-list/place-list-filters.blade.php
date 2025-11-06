{{-- Design moderne compact inspiré Stripe, Linear, Vercel --}}
<div>
    {{-- Header avec bouton reset --}}
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-sm font-medium text-gray-700">Filtres</h2>
        @if($search !== '' || count($tags) > 0)
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
                               placeholder="Nom, description..."
                               class="block w-full rounded-lg border-0 py-2 pl-9 pr-3 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                    </div>
                </div>

                {{-- Section 2: Thématiques (flex-1 pour étalement) --}}
                <div class="lg:px-4 flex-1">
                    <label for="tagSearch" class="block text-xs font-medium text-gray-700 mb-1.5">
                        Thématiques
                        @if(count($tags) > 0)
                            <span class="ml-1 inline-flex items-center justify-center rounded-full bg-blue-100 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700">
                                {{ count($tags) }}
                            </span>
                        @endif
                    </label>

                    {{-- Input recherche tags --}}
                    <div class="relative" x-data="{ open: false }">
                        <input type="text"
                               id="tagSearch"
                               wire:model.live="tagSearchInput"
                               @focus="open = true"
                               @click.away="open = false"
                               placeholder="Ajouter une thématique..."
                               class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600">

                        {{-- Dropdown suggestions --}}
                        @if(count($tagSuggestions) > 0)
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-10 mt-1.5 w-full rounded-lg border border-gray-200 bg-white shadow-xl ring-1 ring-black/5">
                                <ul class="max-h-[30em] overflow-auto py-1">
                                    @php
                                    $availableTags = 0;
                                    @endphp
                                    @foreach($tagSuggestions as $index=>$suggestion)
                                        {{-- Ne pas afficher les tags déjà sélectionnés --}}
                                        @if(!in_array($suggestion['slug'], $tags))
                                            @php
                                                $availableTags ++;
                                            @endphp
                                            <li wire:click="addTag('{{ $suggestion['slug'] }}'); open = false"
                                                class="flex items-center gap-2 cursor-pointer px-3 py-2 text-sm text-gray-900 hover:bg-gray-50 transition-colors">
                                                <x-heroicon-o-plus class="h-3.5 w-3.5 text-gray-400" />
                                                {{ $suggestion['name'] }}
                                            </li>
                                        @endif
                                    @endforeach
                                    @if($availableTags === 0)
                                        <li class="flex items-center gap-2 cursor-pointer px-3 py-2 text-sm text-gray-900 hover:bg-gray-50 transition-colors">
                                            <x-heroicon-o-exclamation-triangle class="w-4 text-gray-400" />
                                            Tous les tags sont sélectionnés
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Tags sélectionnés (inline compact) --}}
                    @if(count($tags) > 0)
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            @foreach($tags as $tagSlug)
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 pl-2 pr-1 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                    {{ $tagSlug }}
                                    <button type="button"
                                            wire:click="removeTag('{{ $tagSlug }}')"
                                            class="inline-flex h-4 w-4 items-center justify-center rounded hover:bg-blue-100 transition-colors">
                                        <x-heroicon-s-x-mark class="h-3 w-3 text-blue-600" />
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- Section 3: Langue (width auto avec min-width) --}}
                <div class="lg:pl-4 lg:min-w-[120px] w-full lg:w-auto">
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">
                        Langue
                    </label>
                    <div class="inline-flex rounded-lg border border-gray-300 bg-gray-50 p-0.5">
                        <button type="button"
                                wire:click="setLocale('fr')"
                                class="{{ $locale === 'fr' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }} flex-1 rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                            FR
                        </button>
                        <button type="button"
                                wire:click="setLocale('en')"
                                class="{{ $locale === 'en' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }} flex-1 rounded-md px-2.5 py-1.5 text-xs font-medium transition-all">
                            EN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
