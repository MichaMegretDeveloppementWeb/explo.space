{{-- Section Localisation - Carte + Coordonnées --}}
<section class="bg-white py-20">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-3xl font-normal text-gray-900 mb-8 text-center">
            {{ __('web/pages/place-show.sections.location') }}
        </h2>

        {{-- Carte Leaflet --}}
        <div
            id="placeMap"
            class="w-full h-96 rounded-lg overflow-hidden bg-gray-200 mb-8 z-1"
            data-latitude="{{ $place->latitude }}"
            data-longitude="{{ $place->longitude }}"
            data-title="{{ $place->title }}"
        ></div>

        {{-- Coordonnées en dessous --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
            @if($place->address)
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-500 mb-2">
                        {{ __('web/pages/place-show.metadata.address') }}
                    </p>
                    <p class="text-base text-gray-900">{{ $place->address }}</p>
                </div>
            @endif

            <div class="text-center">
                <p class="text-sm font-medium text-gray-500 mb-2">
                    {{ __('web/pages/place-show.metadata.coordinates') }}
                </p>
                <p class="text-base text-gray-900 font-mono">
                    {{ number_format($place->latitude, 6) }}, {{ number_format($place->longitude, 6) }}
                </p>
            </div>
        </div>
    </div>
</section>
