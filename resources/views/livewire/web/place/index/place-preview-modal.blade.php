{{-- Modale de prévisualisation de lieu (design sobre et épuré) --}}

<div x-data="{ open: $wire.entangle('isOpen') }">
    {{-- Overlay (backdrop) sobre --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="$wire.closeModal()"
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[9998]"
         style="display: none;">
    </div>

    {{-- Modale centrée --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.self="$wire.closeModal()"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6"
         style="display: none;">

        {{-- Carte sobre --}}
        <div @click.stop
             class="bg-white rounded-xl shadow-md max-w-lg w-full overflow-hidden relative">

            {{-- Bouton X sobre avec fond blanc --}}
            <button @click="$wire.closeModal()"
                    type="button"
                    class="absolute top-3 right-3 z-10 p-2 bg-white/90 backdrop-blur-sm rounded-lg text-gray-600 hover:text-gray-900  transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            @if($place)
                {{-- Photo de couverture sobre --}}
                <div class="relative h-48 sm:h-50 bg-gray-100 overflow-hidden">
                    @if($place->mainPhotoUrl)
                        <img src="{{ $place->mainPhotoUrl }}"
                             alt="{{ $place->title }}"
                             class="w-full h-full object-cover">
                    @else
                        {{-- Placeholder sobre --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Contenu sobre --}}
                <div class="p-6 space-y-4">
                    {{-- Titre --}}
                    <h2 class="text-xl sm:text-2xl font-semibold text-gray-900 leading-tight">
                        {{ $place->title }}
                    </h2>

                    {{-- Description --}}
                    @if($place->descriptionExcerpt)
                        <p class="text-gray-600 text-sm leading-relaxed max-h-[40vh] overflow-auto p-3 my-4 shadow-sm border border-gray-100 rounded-sm">
                            {{ $place->descriptionExcerpt }}
                        </p>
                    @endif

                    {{-- Tags sobres --}}
                    @if(count($place->tags) > 0)
                        <div class="flex flex-wrap gap-2 my-4">
                            @foreach($place->tags as $tag)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md border"
                                      style="background-color: {{ $tag['color'] }}08; color: {{ $tag['color'] }}; border-color: {{ $tag['color'] }}20;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $tag['name'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Bouton CTA sobre avec outline --}}
                    <a href="{{ localRoute('places.show', ['slug' => $place->slug]) }}"
                       class="inline-flex items-center justify-center gap-2 w-full border border-gray-300 hover:border-gray-400 bg-white hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg font-medium text-sm transition-colors">
                        <span>{{ __('web/pages/explore.place_preview.view_detail') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>

            @elseif($errorMessage)
                {{-- Affichage d'erreur sobre --}}
                <div class="p-8 text-center space-y-4">
                    {{-- Icône d'erreur sobre --}}
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-50">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    {{-- Titre et message --}}
                    <div class="space-y-2">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('web/pages/explore.place_preview.error_title') }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $errorMessage }}</p>
                    </div>

                    {{-- Message technique (uniquement en mode développement) --}}
                    @if($technicalError)
                        <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                            <p class="text-xs font-semibold text-yellow-900 mb-1">Debug Info (dev only)</p>
                            <p class="text-xs text-yellow-800 font-mono break-all">{{ $technicalError }}</p>
                        </div>
                    @endif

                    {{-- Bouton fermer sobre --}}
                    <button @click="$wire.closeModal()"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg font-medium transition-colors">
                        {{ __('web/pages/explore.place_preview.close') }}
                    </button>
                </div>
            @endif

        </div>
    </div>
</div>
