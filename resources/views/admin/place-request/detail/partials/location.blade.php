@props(['placeRequest'])

<x-admin.place.detail.info-card
    title="Localisation"
    icon="heroicon-o-map-pin">

    <dl>
        @if($placeRequest->latitude && $placeRequest->longitude)
            <x-admin.place.detail.attribute-row
                label="Coordonnées GPS"
                :value="$placeRequest->latitude . ', ' . $placeRequest->longitude" />
        @else
            <x-admin.place.detail.attribute-row
                label="Coordonnées GPS"
                value="Non renseignées par l'utilisateur" />
        @endif

        <x-admin.place.detail.attribute-row
            label="Adresse"
            :value="$placeRequest->address ?: 'Non renseignée par l\'utilisateur'" />
    </dl>

    {{-- Carte de prévisualisation --}}
    @if($placeRequest->latitude && $placeRequest->longitude)
        <div wire:ignore class="mt-4 pt-4 border-t border-gray-200">
            <div id="place-request-location-preview"
                 class="rounded-lg h-48 z-0"
                 data-latitude="{{ $placeRequest->latitude }}"
                 data-longitude="{{ $placeRequest->longitude }}">
            </div>
        </div>
    @else
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <x-heroicon-o-map class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                    <p class="text-sm">Aucune coordonnée fournie par l'utilisateur</p>
                </div>
            </div>
        </div>
    @endif
</x-admin.place.detail.info-card>
