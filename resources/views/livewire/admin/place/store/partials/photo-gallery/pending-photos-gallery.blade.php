{{-- Galerie unifiée: Photos PlaceRequest + Photos EditRequest + Photos uploadées --}}
@if (count($placeRequestPhotos) > 0 || count($editRequestPhotos) > 0 || count($photos) > 0)
    <div>
        <h3 class="text-sm font-medium text-gray-700 mb-3">
            Photos sélectionnées ({{ count($placeRequestPhotos) + count($editRequestPhotos) + count($photos) }})
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            {{-- Photos PlaceRequest --}}
            @foreach ($placeRequestPhotos as $index => $prPhoto)
                <div class="relative group" wire:key="pr-photo-{{ $prPhoto['id'] }}">
                    {{-- Preview --}}
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-blue-200 transition-all">
                        <img src="{{ $prPhoto['url'] }}"
                             alt="Photo de la proposition"
                             class="w-full h-full object-cover">
                    </div>

                    {{-- Overlay léger au hover --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 rounded-lg pointer-events-none"></div>

                    {{-- Badge "Proposition" (coin supérieur gauche) --}}
                    <div class="absolute top-2 left-2 px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded shadow-lg">
                        Proposition
                    </div>

                    {{-- Menu contextuel (coin supérieur droit) --}}
                    <div class="absolute top-2 right-2">
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md transition-all">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                 style="display: none;">

                                <button type="button"
                                        wire:click="openPhotoTranslationModal('placeRequest_{{ $index }}', 'placeRequest')"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Texte SEO
                                </button>

                                <button type="button"
                                        wire:click="removePlaceRequestPhoto({{ $index }})"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Retirer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Photos EditRequest --}}
            @foreach ($editRequestPhotos as $index => $erPhoto)
                <div class="relative group" wire:key="er-photo-{{ $erPhoto['id'] }}">
                    {{-- Preview --}}
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-purple-200 transition-all">
                        <img src="{{ $erPhoto['url'] }}"
                             alt="Photo proposée (EditRequest)"
                             class="w-full h-full object-cover">
                    </div>

                    {{-- Overlay léger au hover --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 rounded-lg pointer-events-none"></div>

                    {{-- Badge "Proposition" en violet (coin supérieur gauche) --}}
                    <div class="absolute top-2 left-2 px-2 py-1 bg-purple-600 text-white text-xs font-semibold rounded shadow-lg">
                        Proposition
                    </div>

                    {{-- Menu contextuel (coin supérieur droit) --}}
                    <div class="absolute top-2 right-2">
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md transition-all">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                 style="display: none;">

                                <button type="button"
                                        wire:click="openPhotoTranslationModal('editRequest_{{ $erPhoto['edit_request_id'] }}_{{ $index }}', 'editRequest-{{ $erPhoto['edit_request_id'] }}-{{ $index }}')"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Texte SEO
                                </button>

                                <button type="button"
                                        wire:click="removeEditRequestPhoto({{ $index }})"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Retirer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Photos uploadées --}}
            @foreach ($photos as $index => $photo)
                <div class="relative group" wire:key="upload-photo-{{ $index }}">
                    {{-- Preview --}}
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-300 transition-all">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}"
                                 alt="Photo uploadée"
                                 class="w-full h-full object-cover">
                        @endif
                    </div>

                    {{-- Overlay léger au hover --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 rounded-lg pointer-events-none"></div>

                    {{-- Menu contextuel (coin supérieur droit) --}}
                    <div class="absolute top-2 right-2">
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md transition-all">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                 style="display: none;">

                                <button type="button"
                                        wire:click="openPhotoTranslationModal('pending_{{ $index }}', 'pending')"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Texte SEO
                                </button>

                                <button type="button"
                                        wire:click="removePhoto({{ $index }})"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Retirer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="my-4 rounded-md bg-blue-50 border border-blue-200 p-3">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800">
                        @if ($mode === 'create')
                            @if (count($placeRequestPhotos) > 0 || count($editRequestPhotos) > 0)
                                Ces photos seront enregistrées avec le lieu. La première photo de la proposition sera définie comme photo principale.
                            @else
                                Ces photos seront enregistrées avec le lieu. La première sera définie comme photo principale.
                            @endif
                        @else
                            Ces photos seront ajoutées au lieu après enregistrement.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
