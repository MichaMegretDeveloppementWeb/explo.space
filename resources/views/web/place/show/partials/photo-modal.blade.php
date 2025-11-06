{{-- Modal Galerie Photos (Lightbox style Apple) --}}
<div
    id="photoGalleryModal"
    class="fixed inset-0 z-50 hidden bg-white"
    data-photos="{{ json_encode($place->photos) }}"
>
    {{-- Header modal --}}
    <div class="absolute top-0 left-0 right-0 z-10 flex items-center justify-between p-6 bg-gradient-to-b from-black/40 to-transparent">
        <div class="text-white font-medium" id="photoCounter"></div>
        <button
            type="button"
            onclick="closePhotoGallery()"
            class="p-1 rounded-full bg-black/20 backdrop-blur-sm text-white hover:bg-black/40 transition-colors cursor-pointer"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Image principale --}}
    <div class="relative w-full h-full flex items-center justify-center">
        <img
            id="modalPhoto"
            src=""
            alt=""
            class="max-w-full max-h-full object-contain"
        >
    </div>

    {{-- Navigation avec fond noir semi-transparent pour contraste --}}
    <button
        type="button"
        id="prevPhoto"
        onclick="navigatePhoto('prev')"
        class="absolute left-2 lg:left-6 top-1/2 -translate-y-1/2 p-1 py-4 rounded-sm bg-black/20 backdrop-blur-sm text-white hover:bg-black/60 transition-colors cursor-pointer"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <button
        type="button"
        id="nextPhoto"
        onclick="navigatePhoto('next')"
        class="absolute right-2 lg:right-6 top-1/2 -translate-y-1/2 p-1 py-4 rounded-sm bg-black/20 backdrop-blur-sm text-white hover:bg-black/60 transition-colors cursor-pointer"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>
