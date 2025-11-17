{{-- Galerie photos - Grid uniforme --}}
@if(count($place->photos) > 0)
    <section class="bg-gray-50 pt-16">
        <div class="max-w-5xl mx-auto px-6">
            <h2 class="text-3xl font-normal text-gray-900 mb-16 text-center">
                {{ __('web/pages/place-show.sections.photos') }}
            </h2>

            {{-- Grid uniforme - tous les items ont le même aspect ratio --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($place->photos as $photo)
                    <button
                        type="button"
                        data-photo-id="{{ $photo['id'] }}"
                        data-photo-url="{{ $photo['url'] }}"
                        data-photo-alt="{{ $photo['alt_text'] ?? ($place->title . ' - Image ' . $loop->iteration) }}"
                        class="group relative aspect-[4/3] rounded-lg overflow-hidden bg-transparent  hover:opacity-80 transition-opacity cursor-pointer"
                        onclick="openPhotoGallery({{ $loop->index }})"
                    >
                        <img
                            src="{{ $photo['medium_url'] }}"
                            alt="{{ $photo['alt_text'] ?? ($place->title . ' - Image ' . $loop->iteration) }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </button>
                @endforeach
            </div>
        </div>
    </section>

@endif
