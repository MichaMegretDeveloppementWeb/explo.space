{{-- Injection de la configuration PHP vers JavaScript --}}
@push('scripts')
<script>
    // Configuration backend synchronisée vers frontend
    window.PlaceSearchConfig = @json(\App\Support\Config\PlaceSearchConfig::getJsConfig());
</script>
@endpush

{{-- Interface Google Maps avec architecture modulaire refactorisée --}}
<div id="place-explorer-container"
     class="bg-white overflow-hidden"
     :class="{ 'sidebar-collapsed': sidebarCollapsed }"
     style="font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;"
     x-data="{
        sidebarCollapsed: false,

        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;

            // Attendre la fin de l'animation CSS (300ms) puis redimensionner la carte
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('sidebar:toggled', {
                    detail: { collapsed: this.sidebarCollapsed }
                }));
            }, 300);
        }
     }">

    {{-- Composants instanciés UNE SEULE FOIS --}}
    {{-- Le layout desktop/mobile est géré par CSS (index.css) --}}

    {{-- Wrapper sidebar (desktop only, géré par CSS) --}}
    <div class="sidebar-wrapper">
        <livewire:web.place.index.place-filters :initialFilters="$initialFilters" />
        <livewire:web.place.index.place-list :initialFilters="$initialFilters" />
    </div>

    <livewire:web.place.index.place-map :initialFilters="$initialFilters"/>

    {{-- Modale de prévisualisation de lieu --}}
    <livewire:web.place.index.place-preview-modal />

    {{-- Bouton toggle sidebar (desktop only) --}}
    <button @click="toggleSidebar()"
            class="sidebar-toggle-btn"
            :title="sidebarCollapsed ? 'Ouvrir la sidebar' : 'Fermer la sidebar'"
            type="button">
        <svg x-show="!sidebarCollapsed"
             class="w-5 h-5 text-gray-600"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <svg x-show="sidebarCollapsed"
             class="w-5 h-5 text-gray-600"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

</div>
