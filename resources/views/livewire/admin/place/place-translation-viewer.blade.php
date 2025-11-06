<x-admin.place.detail.info-card
    title="Traductions"
    icon="heroicon-o-language"
    :count="$translations->count()">

    @if($translations->isEmpty())
        <p class="text-sm text-gray-500 italic">Aucune traduction disponible</p>
    @else
        {{-- Barre de switch de langues --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex -mb-px space-x-4" aria-label="Tabs">
                @foreach($translations as $translation)
                    <button
                        wire:click="selectLocale('{{ $translation->locale }}')"
                        class="group inline-flex items-center px-3 py-3 border-b-2 font-medium text-sm transition-colors duration-150
                            {{ $selectedLocale === $translation->locale
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">

                        {{-- Drapeau ou code langue --}}
                        <span class="uppercase font-semibold">{{ $translation->locale }}</span>

                        {{-- Badge statut --}}
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $translation->status === 'published'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $translation->status === 'published' ? 'Publié' : 'Brouillon' }}
                        </span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Contenu de la traduction sélectionnée --}}
        @if($selectedTranslation)
            <div>
                {{-- Bouton Modifier (commenté - fonctionnalité future : édition modale) --}}
                {{--
                <div class="flex justify-start mb-6">
                    <a href="#"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <x-heroicon-o-pencil class="h-4 w-4 mr-2" />
                        Modifier cette traduction
                    </a>
                </div>
                --}}

                {{-- Détails de la traduction --}}
                <div class="space-y-6">
                    {{-- Titre et Slug --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Titre</dt>
                            <dd class="text-base font-medium text-gray-900">
                                {{ $selectedTranslation->title }}
                            </dd>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Slug</dt>
                            <dd class="text-sm text-gray-700 font-mono">
                                {{ $selectedTranslation->slug }}
                            </dd>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <dt class="flex items-center text-sm font-medium text-gray-700 mb-3">
                            <x-heroicon-o-document-text class="h-5 w-5 mr-2 text-gray-400" />
                            Description
                        </dt>
                        <dd class="bg-white border border-gray-200 rounded-lg p-5 text-sm text-gray-900 shadow-sm">
                            {{ $selectedTranslation->description }}
                        </dd>
                    </div>

                    {{-- Informations pratiques --}}
                    @if($selectedTranslation->practical_info)
                        <div>
                            <dt class="flex items-center text-sm font-medium text-gray-700 mb-3">
                                <x-heroicon-o-information-circle class="h-5 w-5 mr-2 text-gray-400" />
                                Informations pratiques
                            </dt>
                            <dd class="bg-blue-50 border border-blue-200 rounded-lg p-5 text-sm text-gray-900 shadow-sm">
                                {{ $selectedTranslation->practical_info }}
                            </dd>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500 italic">
                                Aucune information pratique renseignée pour cette traduction
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <x-heroicon-o-exclamation-triangle class="h-10 w-10 text-yellow-600 mx-auto mb-3" />
                <p class="text-sm font-medium text-yellow-900 mb-1">
                    Aucune traduction disponible
                </p>
                <p class="text-sm text-yellow-700">
                    La langue sélectionnée ne possède pas encore de traduction.
                </p>
            </div>
        @endif
    @endif
</x-admin.place.detail.info-card>
