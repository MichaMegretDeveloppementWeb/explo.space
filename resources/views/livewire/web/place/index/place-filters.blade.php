{{-- PlaceFilters Component - Responsive (desktop sidebar / mobile top sheet) --}}

<div class="place-filters bg-white shadow-2xl lg:shadow-none rounded-b-2xl lg:rounded-none flex flex-col lg:flex-col"
     x-data="{
        filtersCollapsed: @entangle('filtersCollapsed'),
        isExpanded: false,
        isMobile: window.innerWidth < 1024,

        init() {
            // Détecter changement de taille d'écran
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
            });
        },

        toggleFilters() {
            this.filtersCollapsed = !this.filtersCollapsed;
        },

        toggleSheet() {
            this.isExpanded = !this.isExpanded;
        },

        getMobileStyle() {
            if (!this.isMobile) return '';
            return 'transform: translateY(' + (this.isExpanded ? '0' : 'calc(-100% + 50px)') + '); transition: transform 300ms ease;';
        }
     }"
     :style="getMobileStyle()">


    {{-- Header (desktop toggle button / mobile toggle bar) --}}
    <div class="filters-header">
        {{-- Desktop header (toggle button) --}}
        <div class="filters-header-desktop">
            @include('livewire.web.place.index.partials.filters.filters-toggle-button')
        </div>

        {{-- Mobile header (toggle bar) --}}
        <div class="filters-header-mobile" @click="toggleSheet()">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-900">
                    {{ __('web/pages/explore.livewire.filters_title') }}
                    @if(count($selectedTags) > 0)
                        <span class="text-xs text-gray-500 ml-1">
                            • {{ count($selectedTags) }} {{ __('web/pages/explore.livewire.filters_count') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Icône toggle mobile --}}
            <div class="flex-shrink-0 ml-3">
                <svg class="w-6 h-6 text-gray-600 transition-transform duration-300"
                     :class="isExpanded ? '' : 'rotate-180'"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Zone de filtres (collapsible desktop / scrollable content mobile) --}}
    <div class="filters-content p-2 pb-4"
         x-show="!filtersCollapsed"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         x-cloak>

        {{-- Message d'erreur de validation --}}
        @error('filters_validation')
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3">
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

        {{-- Mode selector --}}
        <div class="filters-mode-selector">
            @include('livewire.web.place.index.partials.filters.mode-selector')
        </div>

        {{-- Address search (proximity mode only) --}}
        @if($searchMode === 'proximity')
            <div class="filters-address-search mt-4">
                @include('livewire.web.place.index.partials.filters.address-search')
            </div>
        @endif

        {{-- Controls section --}}
        <div class="filters-controls">
            {{-- Radius control (proximity mode only) --}}
            @if($searchMode === 'proximity')
                <div class="filters-radius mt-2">
                    @include('livewire.web.place.index.partials.filters.radius-control')
                </div>
            @endif

            {{-- Tag filtering --}}
            <div class="filters-tags mt-4">
                <div class="flex items-center justify-between">
                    @if(count($selectedTags) > 0)
                        <button wire:click="clearAllTags" class="text-xs text-red-600 hover:text-red-800">
                            {{ __('web/pages/explore.livewire.clear_all_tags') }}
                        </button>
                    @endif
                </div>

                {{-- Selected tags --}}
                @if(count($selectedTags) > 0)
                    <div class="mt-2">
                        @include('livewire.web.place.index.partials.filters.tag-chips')
                    </div>
                @endif

                {{-- Tag search with autocomplete --}}
                <div class="mt-2">
                    @include('livewire.web.place.index.partials.filters.tag-search')
                </div>
            </div>
        </div>

    </div>
</div>
