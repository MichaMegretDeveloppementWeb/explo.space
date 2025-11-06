@props([
    'photos' => [],
    'placeId' => null,
])

@if($photos->isEmpty())
    <div class="text-center py-12">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
            <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
        </div>
        <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune photo enregistrée</h3>
        <p class="text-sm text-gray-500 mb-6">Ajoutez des photos pour illustrer ce lieu</p>
        <a href="{{ route('admin.places.edit', $placeId) }}#photos"
           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-900 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
            <x-heroicon-o-plus class="h-4 w-4 mr-2" />
            Ajouter une première photo
        </a>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($photos as $photo)
            <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 {{ $photo->is_main ? 'border-blue-500' : 'border-gray-200' }}">
                <img src="{{ $photo->url }}"
                     alt="{{ $photo->alt_text }}"
                     class="w-full h-full object-cover">

                @if($photo->is_main)
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-600 text-white shadow-sm">
                            <x-heroicon-s-star class="h-3 w-3 mr-1" />
                            Principale
                        </span>
                    </div>
                @endif

                {{-- Overlay hover --}}
                <div class="absolute inset-0 bg-transparent group-hover:bg-black/50 group-hover:backdrop-blur-xs group-hover:bg-opacity-70 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <a href="{{ $photo->url }}"
                       target="_blank"
                       class="inline-flex items-center px-3 py-1.5 bg-white rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <x-heroicon-o-magnifying-glass-plus class="h-4 w-4 mr-1" />
                        Voir
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
