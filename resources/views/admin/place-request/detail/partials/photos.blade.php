@props(['placeRequest', 'photoCount'])

<x-admin.place.detail.info-card
    title="Photos proposées"
    icon="heroicon-o-photo"
    :count="$photoCount">

    @if($placeRequest->photos->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($placeRequest->photos as $photo)
                <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 group">
                    <img src="{{ $photo->url }}"
                         alt="{{ $photo->original_name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">

                    {{-- Overlay avec infos --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="absolute bottom-0 left-0 right-0 p-2">
                            <p class="text-white text-xs truncate">{{ $photo->original_name }}</p>
                            <p class="text-white/80 text-xs">{{ number_format($photo->size / 1024, 0) }} Ko</p>
                        </div>
                    </div>

                    {{-- Badge numéro --}}
                    <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-medium">
                            {{ $loop->iteration }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Info technique --}}
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-start gap-2 text-sm text-gray-600">
                <x-heroicon-o-information-circle class="h-5 w-5 text-gray-400 flex-shrink-0" />
                <p>
                    <span class="font-medium">Note :</span> Les miniatures seront générées automatiquement lors de l'acceptation de la proposition.
                </p>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <x-heroicon-o-photo class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500">Aucune photo proposée</p>
        </div>
    @endif
</x-admin.place.detail.info-card>
