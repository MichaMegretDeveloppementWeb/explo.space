@props(['place', 'translationCount'])

<x-admin.place.detail.info-card
    title="Traductions"
    icon="heroicon-o-language"
    :count="$translationCount">

    @if($place->translations->isEmpty())
        <div class="text-center py-8">
            <x-heroicon-o-language class="mx-auto h-12 w-12 text-gray-400" />
            <p class="mt-2 text-sm text-gray-500">Aucune traduction disponible</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($place->translations->sortBy('locale') as $translation)
                <div class="border border-gray-200 rounded-lg p-4">
                    {{-- Header traduction --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-semibold text-gray-900 uppercase">
                                {{ $translation->locale }}
                            </span>
                            <x-admin.place.detail.status-badge :status="$translation->status" />
                        </div>
                        <a href="{{ route('admin.places.edit', $place) }}#translation-{{ $translation->locale }}"
                           class="text-sm text-blue-600 hover:text-blue-800">
                            Modifier
                        </a>
                    </div>

                    {{-- Contenu traduction --}}
                    <div class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Titre</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $translation->title }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-600 font-mono">{{ $translation->slug }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 line-clamp-3">
                                {{ $translation->description }}
                            </dd>
                        </div>

                        @if($translation->practical_info)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Informations pratiques</dt>
                                <dd class="mt-1 text-sm text-gray-900 line-clamp-2">
                                    {{ $translation->practical_info }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-admin.place.detail.info-card>
