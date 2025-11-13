{{-- PlaceMap Component - Leaflet interactive map --}}
{{-- Note: Les erreurs de validation sont affichées via une modale gérée par JavaScript (événement 'show-error-modal') --}}
<div class="place-map relative h-full">

    {{-- Map loader (affiché par défaut) --}}
    <div id="map-loader" class="absolute inset-0 z-50 flex items-center justify-center bg-gray-100">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 animate-spin text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-sm text-gray-600">{{ __('web/common.loading_map') }}</p>
        </div>
    </div>

    {{-- Wrapper avec wire:ignore pour protéger Leaflet --}}
    <div wire:ignore class="w-full h-full">
        {{-- Map container --}}
        <div
            id="place-map"
            data-latitude="{{ $currentFilters['latitude'] ?? '' }}"
            data-longitude="{{ $currentFilters['longitude'] ?? '' }}"
            data-radius="{{ $currentFilters['radius'] ?? '' }}"
            data-places="{{ json_encode($coordinates) }}"
            data-use-bounding-box="{{ $useBoundingBox ? 'true' : 'false' }}"
            class="w-full h-full opacity-0 transition-opacity duration-300"
        ></div>
    </div>


    {{-- Error display (if any) --}}
    @error('coordinates_loading')
        <div class="map-error-overlay">
            <h3>{{ __('errors/general.error_occurred') }}</h3>
            <p>{{ $message }}</p>
            <button
                wire:click="$refresh"
                type="button"
            >
                {{ __('web/common.retry') }}
            </button>
        </div>
    @enderror
</div>
