@props(['placeRequest'])

<x-admin.place.detail.info-card
    title="Informations générales"
    icon="heroicon-o-information-circle">

    <dl>
        <x-admin.place.detail.attribute-row
            label="Titre"
            :value="$placeRequest->title" />

        <x-admin.place.detail.attribute-row label="Description" type="custom">
            <div class="prose prose-sm max-w-none text-gray-900">
                {!! nl2br(e($placeRequest->description)) !!}
            </div>
        </x-admin.place.detail.attribute-row>

        @if($placeRequest->practical_info)
            <x-admin.place.detail.attribute-row label="Informations pratiques" type="custom">
                <div class="prose prose-sm max-w-none text-gray-900">
                    {!! nl2br(e($placeRequest->practical_info)) !!}
                </div>
            </x-admin.place.detail.attribute-row>
        @endif

        <x-admin.place.detail.attribute-row label="Email de contact" type="custom">
            <a href="mailto:{{ $placeRequest->contact_email }}"
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-medium text-base">
                <x-heroicon-o-envelope class="h-5 w-5" />
                {{ $placeRequest->contact_email }}
            </a>
        </x-admin.place.detail.attribute-row>

        <x-admin.place.detail.attribute-row label="Langue détectée" type="badge">
            @if($placeRequest->detected_language && $placeRequest->detected_language !== 'unknown')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    {{ strtoupper($placeRequest->detected_language) }}
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    Langue non détectée
                </span>
            @endif
        </x-admin.place.detail.attribute-row>
    </dl>
</x-admin.place.detail.info-card>
