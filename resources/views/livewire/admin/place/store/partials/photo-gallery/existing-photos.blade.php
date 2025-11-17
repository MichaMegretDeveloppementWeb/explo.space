{{-- Existing Photos (Edit Mode) --}}
@if (count($existingPhotos) > 0)
    <div x-data="photoSortable()" x-init="initSortable()">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Photos existantes</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" id="existing-photos-grid">
            @foreach ($existingPhotos as $photo)
                @php
                    $isMainPhoto = $mainPhotoId === $photo['id'];
                @endphp
                <div class="relative group sortable-item"
                     wire:key="photo-{{ $photo['id'] }}"
                     data-photo-id="{{ $photo['id'] }}"
                     data-sort-order="{{ $photo['sort_order'] }}">

                    {{-- Photo Preview --}}
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 {{ $isMainPhoto ? 'border-blue-500' : 'border-gray-200' }} transition-all">
                        <img src="{{ $photo['medium_url'] }}"
                             alt="Photo du lieu"
                             class="w-full h-full object-cover">
                    </div>

                    {{-- Overlay léger au hover --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-200 rounded-lg pointer-events-none"></div>

                    {{-- Badge Principale (coin supérieur gauche) --}}
                    @if ($isMainPhoto)
                        <div class="absolute top-2 left-2 px-2 py-1 bg-blue-600 text-white text-xs font-semibold rounded shadow-lg flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            Principale
                        </div>
                    @endif

                    {{-- Actions (coin supérieur droit) --}}
                    <div class="absolute top-2 right-2 flex gap-1">
                        {{-- Drag Handle --}}
                        <div class="sortable-handle p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm cursor-move hover:bg-white hover:shadow-md transition-all">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        {{-- Menu dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md transition-all">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            {{-- Dropdown menu --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                 style="display: none;">

                                @if (!$isMainPhoto)
                                    <button type="button"
                                            wire:click="setMainPhoto({{ $photo['id'] }})"
                                            @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        Définir comme principale
                                    </button>
                                @endif

                                <button type="button"
                                        wire:click="openPhotoTranslationModal('existing_{{ $photo['id'] }}', 'existing')"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                    </svg>
                                    Texte SEO
                                </button>

                                <button type="button"
                                        wire:click="deletePhoto({{ $photo['id'] }})"
                                        @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
