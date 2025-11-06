{{-- Métadonnées - Discret en bas de page --}}
<section class="bg-gray-50 py-8 border-t border-gray-200">
    <div class="max-w-3xl mx-auto px-6">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6 text-sm text-gray-500">
            <p>
                <span class="font-medium text-gray-700">{{ __('web/pages/place-show.metadata.added_on') }}</span> {{ $place->createdAt }}
            </p>
            <span class="hidden sm:inline text-gray-300">•</span>
            <p>
                <span class="font-medium text-gray-700">{{ __('web/pages/place-show.metadata.last_updated') }}</span> {{ $place->updatedAt }}
            </p>
        </div>
    </div>
</section>
