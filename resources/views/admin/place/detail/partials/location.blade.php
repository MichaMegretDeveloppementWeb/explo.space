@props(['place'])

<x-admin.place.detail.info-card
    title="Localisation"
    icon="heroicon-o-map-pin">

    <dl>
        @if($place->latitude && $place->longitude)
            <x-admin.place.detail.attribute-row
                label="Coordonnées GPS"
                :value="$place->latitude . ', ' . $place->longitude" />
        @else
            <x-admin.place.detail.attribute-row
                label="Coordonnées GPS"
                value="Non renseignées" />
        @endif

        <x-admin.place.detail.attribute-row
            label="Adresse"
            :value="$place->address ?: 'Non renseignée'" />
    </dl>

    {{-- Carte de prévisualisation --}}
    @if($place->latitude && $place->longitude)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div id="place-location-preview"
                 class="rounded-lg h-48 z-0"
                 data-latitude="{{ $place->latitude }}"
                 data-longitude="{{ $place->longitude }}">
            </div>
        </div>
    @endif
</x-admin.place.detail.info-card>
