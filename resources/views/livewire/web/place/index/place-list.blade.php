{{-- PlaceList Component - Results display (responsive) --}}

<div class="place-list bg-white shadow-2xl lg:shadow-none rounded-t-2xl lg:rounded-none overflow-hidden flex flex-col lg:flex-col"
     x-data="{
        isExpanded: false,
        isMobile: window.innerWidth < 1024,

        init() {
            // Détecter changement de taille d'écran
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
            });
        },

        toggleSheet() {
            this.isExpanded = !this.isExpanded;
        },

        getMobileStyle() {
            if (!this.isMobile) return '';
            return 'bottom: ' + (this.isExpanded ? '0' : 'calc(-100% + 50px)') + '; transition: bottom 300ms ease;';
        }
     }"
     :style="getMobileStyle()">

    {{-- Header (desktop) / Toggle bar (mobile) --}}
    <div class="place-list-header">
        {{-- Desktop header --}}
        <div class="place-list-header-desktop">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('web/pages/explore.livewire.results_title') }}
                </h2>
            </div>
        </div>

        {{-- Mobile toggle bar --}}
        <div class="place-list-header-mobile" @click="toggleSheet()">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-900 flex justify-between items-center">
                    <span class="text-[110%] text-gray-500">{{ __('web/pages/explore.livewire.results_title') }}</span>
                </div>
            </div>

            {{-- Icône toggle mobile --}}
            <div class="flex-shrink-0 ml-3">
                <svg class="w-6 h-6 text-gray-600 transition-transform duration-300"
                     :class="isExpanded ? 'rotate-180' : ''"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </div>
        </div>
    </div>



    {{-- Results list (même contenu desktop/mobile) --}}
    <div id="place-list-results" class="place-list-content">
        {{-- Message d'erreur de validation (défense en profondeur) --}}
        @error('filters_validation')
            <div class="m-4 rounded-lg bg-red-50 border border-red-200 p-3">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-400 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-red-800">{{ $message }}</p>
                    </div>
                    <button wire:click="dismissValidationError"
                            class="ml-3 flex-shrink-0 text-red-400 hover:text-red-600 focus:outline-none">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @enderror

        {{-- Erreur de chargement des lieux --}}
        @error('places_loading')
            <div class="m-4 rounded-lg bg-red-50 border border-red-200 p-3">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-red-400 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-red-800">{{ $message }}</p>
                    </div>
                </div>
            </div>
        @enderror

        @if(!$hasBoundingBox)

            <div class="mt-2 flex items-center justify-center text-md text-gray-500 w-full px-2 py-6">
                <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full mr-2"></div>
                {{ __('web/pages/explore.livewire.loading') }}
            </div>

        @else

            <div wire:loading.delay.longer class="w-full">
                <div wire:loading.flex class="mt-2 flex items-center justify-center text-md text-gray-500 w-full px-2 py-6">
                    <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full mr-2"></div>
                    {{ __('web/pages/explore.livewire.loading') }}
                </div>
            </div>

            <div>
                @if(!empty($places))
                    <div class="divide-y divide-gray-200">
                        @foreach($places as $place)
                            <div class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                                 data-place-id="{{ $place['id'] }}">
                                <div class="flex items-start space-x-3">
                                    @if(!empty($place['main_photo']))
                                        <img src="{{ $place['main_photo']['thumb_url'] }}"
                                             alt="{{ $place['translation']['title'] }}"
                                             class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $place['translation']['title'] }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                            {{ Str::limit(strip_tags($place['translation']['description']), 100) }}
                                        </p>

                                        @if(!empty($place['tags']))
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach(array_slice($place['tags'], 0, 4) as $tag)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                🚀 {{ $tag['name'] }}
                                            </span>
                                                @endforeach
                                                @if(count($place['tags']) > 2)
                                                    <span class="text-xs text-gray-500">+{{ count($place['tags']) - 2 }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Distance (mode proximity) --}}
                                        @if(isset($currentFilters['mode']) && $currentFilters['mode'] === 'proximity' && isset($place['distance']))
                                            <div class="flex items-center mt-2 text-xs text-gray-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                                {{ number_format($place['distance']/1000, 1) }} {{ __('web/pages/explore.livewire.radius_unit') }}
                                            </div>
                                        @endif

                                        {{-- Adresse (mode worldwide) --}}
                                        @if(isset($currentFilters['mode']) && $currentFilters['mode'] === 'worldwide' && !empty($place['address']))
                                            <div class="flex items-center mt-2 text-xs text-gray-500">
                                                <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="truncate">{{ $place['address'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($hasMorePages)
                        {{-- Sentinelle invisible détectée par Intersection Observer --}}
                        <div wire:ignore
                             x-data="{
                        observer: null,

                        init() {
                            this.observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        @this.loadMore();
                                    }
                                });
                            }, {
                                root: null,
                                rootMargin: '100px',
                                threshold: 0.1
                            });

                            this.observer.observe(this.$el);

                            // Écouter l'événement de reset de liste
                            Livewire.on('list-reset', () => {
                                // Remettre le scroll en haut du conteneur de résultats
                                const container = document.getElementById('place-list-results');
                                if (container) {
                                    container.scrollTop = 0;
                                }
                            });
                        }
                     }"
                             class="h-px"></div>

                        {{-- Loader pendant chargement --}}
                        <div wire:loading wire:target="loadMore" class="p-8 text-center w-full">
                            <div class="inline-flex items-center space-x-2 text-sm text-gray-500">
                                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('web/pages/explore.livewire.loading_more') }}</span>
                            </div>
                        </div>
                    @else
                        {{-- Plus de résultats --}}
                        @if(count($places) > 0)
                            <div class="p-6 text-center border-t border-gray-200">
                                <p class="text-sm text-gray-500">
                                    {{ __('web/pages/explore.livewire.no_more_results') }}
                                </p>
                            </div>
                        @endif
                    @endif
                @else
                    {{-- Distinguer les deux états visuellement --}}
                    @if(!$this->isMinimalConditionsMet)
                        {{-- État 1 : Conditions minimales NON réunies (Bleu, encourageant) --}}
                        @include('livewire.web.place.index.partials.list.start-search', [
                            'title' => __('web/pages/explore.livewire.empty_state_start_search_title'),
                            'message' => $this->getStartSearchMessage()
                        ])
                    @else
                        {{-- État 2 : Recherche effectuée SANS résultats (Gris, neutre) --}}
                        @include('livewire.web.place.index.partials.list.no-results', [
                            'title' => __('web/pages/explore.livewire.no_results_title'),
                            'message' => $this->getNoResultsMessage(),
                        ])
                    @endif
                @endif
            </div>

        @endif


    </div>

</div>
