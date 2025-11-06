{{-- Informations pratiques --}}
@if($place->practicalInfo)
    <section class="bg-white py-12 mb-10">
        <div class="max-w-5xl mx-auto px-6">
            <h2 class="text-3xl font-normal text-gray-900 mb-6">
                {{ __('web/pages/place-show.sections.practical_info') }}
            </h2>
            <div class="text-md text-gray-700 leading-relaxed space-y-4">
                {!! nl2br(e($place->practicalInfo)) !!}
            </div>
        </div>
    </section>
@endif
