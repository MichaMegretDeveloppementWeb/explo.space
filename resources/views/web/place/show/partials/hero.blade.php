{{-- Hero Section - Simple et immersif (style Apple) --}}
<section class="relative w-full h-[40vh] min-h-[300px] max-h-[700px] bg-black">
    @if($place->mainPhotoUrl)
        <img
            src="{{ $place->mainPhotoUrl }}"
            alt="{{ $place->title }}"
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
        </div>
    </div>
</section>
