{{-- Hero Section - Simple et immersif (style Apple) --}}
<section class="relative w-full h-[40vh] min-h-[300px] max-h-[700px] bg-black">
    @if($place->mainPhotoUrl)
        <img
            src="{{ $place->mainPhotoUrl }}"
            alt="{{ $place->mainPhotoAltText ?? $place->title }}"
            class="w-full h-full object-cover"
        >
        {{-- Overlay gradient subtil --}}
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black"></div>
    @else
        {{-- Fallback si pas de photo --}}
        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
    @endif

    {{-- Titre centré en bas --}}
    <div class="absolute inset-x-0 bottom-0 pb-10">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-5xl md:text-6xl font-semibold text-white mb-4 tracking-tight">
                {{ $place->title }}
            </h1>

            {{-- Badge "Lieu à la une" si featured --}}
            @if($place->isFeatured)
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-purple-500 to-purple-700 text-white text-sm font-medium shadow-lg">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>{{ __('web/common.featured_place_badge') }}</span>
                </div>
            @endif
        </div>
    </div>
</section>
