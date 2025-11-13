@php use App\Models\Photo; @endphp
<!-- Section Lieux emblematiques -->
<section class="py-12 sm:py-16 md:py-20 bg-gray-50 px-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <div class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-blue-100 text-blue-800 text-xs sm:text-sm font-medium mb-3 sm:mb-4">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full mr-1.5 sm:mr-2"></span>
                {{ __('web/pages/home.featured_places.badge') }}
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                {{ __('web/pages/home.featured_places.title') }}
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                {{ __('web/pages/home.featured_places.subtitle') }}
            </p>
        </div>

        <div class="flex flex-wrap justify-center items-stretch gap-6 sm:gap-8 max-w-6xl mx-auto">
            @forelse($featuredPlaces as $place)

                @php
                    $translation = $place->translations->first();
                    /** @var Photo $mainPhoto */
                    $mainPhoto = $place->photos->first();
                    $tag = $place->tags->first();
                    $tagTranslation = $tag?->translations->first();
                @endphp

                @if($translation)
                <a href="{{ localRoute('places.show', ['slug' => $translation->slug]) }}"
                   class="flex-1 min-w-[300px] max-w-[400px] group bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden hover:shadow-xl sm:hover:shadow-2xl transition-all duration-300 block">
                    <div class="h-40 sm:h-48 bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center relative overflow-hidden">
                        @if($mainPhoto?->medium_url)
                            <img src="{{ $mainPhoto->medium_url }}"
                                 alt="{{ $translation->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ $translation->title }}</span>
                            </div>
                        @endif

                        @if($tagTranslation)
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 bg-white/90 text-xs font-medium rounded-full"
                                      style="color: {{ $tag->color ?? '#6366f1' }}">
                                    {{ $tagTranslation->name }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">{{ $translation->title }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed mb-3 sm:mb-4 line-clamp-2">{{ $translation->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs sm:text-sm text-gray-500">{{ $place->address }}</span>
                            <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 group-hover:translate-x-1 transition-transform"
                                                      style="color: {{ $tag->color ?? '#6366f1' }}" />
                        </div>
                    </div>
                </a>
                @endif
            @empty
                <!-- Fallback si aucun lieu featured -->
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">{{ __('web/pages/home.featured_places.no_places') }}</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-8 sm:mt-12">
            <a href="{{ localRoute('explore', ['mode' => 'worldwide', 'featured' => true]) }}" class="inline-flex items-center px-6 sm:px-8 py-3 sm:py-4 bg-gray-900 text-white font-semibold rounded-lg sm:rounded-xl hover:bg-gray-800 transition-colors text-sm sm:text-base">
                {{ __('web/pages/home.featured_places.cta') }}
                <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 ml-2" />
            </a>
        </div>
    </div>
</section>
