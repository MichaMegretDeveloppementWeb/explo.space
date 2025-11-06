@props(['place'])

<x-admin.place.detail.info-card
    title="Traçabilité & Statut"
    icon="heroicon-o-clock">

    <dl>
        {{-- Statut actuel --}}
        <x-admin.place.detail.attribute-row label="Statut" type="badge">
            @if($place->is_featured)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-300">
                    <x-heroicon-s-star class="h-3 w-3 mr-1" />
                    À l'affiche
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                    Standard
                </span>
            @endif
        </x-admin.place.detail.attribute-row>

        {{-- Créé par --}}
        @if($place->admin)
            <x-admin.place.detail.attribute-row
                label="Créé par"
                :value="$place->admin->name" />
        @endif

        {{-- Proposition d'origine --}}
        @if($place->placeRequest)
            <x-admin.place.detail.attribute-row label="Origine" type="link">
                <a href="{{ route('admin.place-requests.show', $place->placeRequest->id) }}"
                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline">
                    <x-heroicon-o-document-text class="h-4 w-4" />
                    <span>Issu d'une proposition</span>
                    <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                </a>
            </x-admin.place.detail.attribute-row>
        @endif

        {{-- Date de création --}}
        <x-admin.place.detail.attribute-row
            label="Date de création"
            :value="$place->created_at->format('d/m/Y à H:i')" />

        {{-- Dernière modification --}}
        <x-admin.place.detail.attribute-row
            label="Dernière modification"
            :value="$place->updated_at->format('d/m/Y à H:i')" />
    </dl>

</x-admin.place.detail.info-card>
