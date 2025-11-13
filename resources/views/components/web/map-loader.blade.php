{{--
    Map Loader Component (Web version)

    Affiche un loader qui se superpose à une carte Leaflet pendant le géocodage
    Utilise Alpine.js pour détecter les événements DOM émis par le JavaScript de la carte
--}}

<div x-data="{
    isLoading: false,
    init() {
        // Afficher le loader au début du géocodage
        window.addEventListener('map-geocoding-started', () => {
            this.isLoading = true;
        });

        // Masquer le loader à la fin du géocodage (émis par Livewire)
        window.addEventListener('map-geocoding-finished', () => {
            this.isLoading = false;
        });
    }
}"
     x-show="isLoading"
     x-transition.opacity
     class="absolute inset-0 bg-white/70 backdrop-blur-sm z-[1000] rounded-lg flex items-center justify-center">
    <div class="flex flex-col items-center justify-center space-y-3">
        {{-- Spinner --}}
        <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        {{-- Message --}}
        <div class="text-sm font-medium text-gray-700">
            {{ __('web/common.loading_coordinates') }}
        </div>
    </div>
</div>
