{{-- Alpine.js : validation limites PHP critiques (upload_max_filesize + post_max_size) --}}
<div class="space-y-6"
     x-data="{
        uploadMaxSizeMB: {{ \App\Helpers\UploadHelper::getPhpUploadMaxSizeMB() }},
        postMaxSizeMB: {{ \App\Helpers\UploadHelper::getPostMaxSizeMB() }},
        phpLimitError: null,

        validateBeforeUpload(event) {
            // Clear l'erreur Alpine.js
            this.phpLimitError = null;

            @this.call('clearPendingPhotosErrors');

            const files = event.target.files;
            if (!files || files.length === 0) {
                return;
            }

            let totalSizeMB = 0;

            // Vérifier chaque fichier individuellement et calculer le total
            for (let i = 0; i < files.length; i++) {
                const fileSizeMB = files[i].size / (1024 * 1024);
                totalSizeMB += fileSizeMB;

                // Vérification 1 : Fichier individuel > upload_max_filesize
                if (fileSizeMB > this.uploadMaxSizeMB) {
                    this.phpLimitError = `Le fichier &quot;${files[i].name}&quot; (${fileSizeMB.toFixed(1)} Mo) dépasse la taille limite autorisée.`;
                    event.target.value = '';
                    event.stopImmediatePropagation(); // BLOQUE wire:model.live
                    return;
                }
            }

            // Vérification 2 : Total tous fichiers > post_max_size
            if (totalSizeMB > this.postMaxSizeMB) {
                this.phpLimitError = `La taille totale des fichiers (${totalSizeMB.toFixed(1)} Mo) dépasse la taille limite cumulée autorisée. Veuillez sélectionner moins de fichiers ou des fichiers plus petits.`;
                event.target.value = '';
                event.stopImmediatePropagation(); // BLOQUE wire:model.live
                return;
            }

        }
     }">

    {{-- Existing Photos (Edit Mode) --}}
    @if ($mode === 'edit' && count($existingPhotos) > 0)
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

    {{-- Upload New Photos --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 mb-3">
            {{ $mode === 'edit' ? 'Ajouter de nouvelles photos' : 'Photos' }}
        </h3>

        <div class="space-y-4">
            {{-- Upload Area --}}
            <div id="photo-drop-zone" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex flex-wrap items-center justify-center text-sm text-gray-600 my-4">
                        <label for="photos" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                            <span>Télécharger des photos</span>
                            <input id="photos"
                                   type="file"
                                   wire:model.live="pendingPhotos"
                                   multiple
                                   accept="image/jpeg,image/jpg,image/png,image/webp"
                                   @change.capture="validateBeforeUpload($event)"
                                   class="sr-only">
                        </label>
                        <p class="pl-1">ou glisser-déposer</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        JPEG, PNG, WebP jusqu'à {{ round(config('upload.images.max_size_kb') / 1024, 1) }} Mo (max {{ config('upload.images.max_files') }} photos)
                    </p>
                    <p class="text-xs text-gray-400 mt-3">
                        Limites cumulées : <span x-text="postMaxSizeMB"></span> Mo total
                    </p>
                </div>
            </div>

            {{-- Message de succès --}}
            @if (session()->has('photo_success'))
                <div class="rounded-md bg-green-50 border border-green-200 p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">{{ session('photo_success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Zone d'erreurs --}}
            <div class="space-y-2">

                {{-- 2. Erreur Alpine.js (limite PHP détectée avant upload - info uniquement) --}}
                <div x-show="phpLimitError"
                     x-transition
                     class="rounded-md bg-red-50 border border-red-200 p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-s-x-circle class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="error-message text-sm text-red-800" x-html="phpLimitError"></p>
                        </div>
                    </div>
                </div>

                {{-- 3. Erreurs Livewire validation (pendingPhotos) --}}
                {{-- Erreurs globales pendingPhotos --}}
                @error('pendingPhotos')
                    <div class="rounded-md bg-red-50 border border-red-200 p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-x-circle class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <p class="error-message text-sm text-red-800">{{ $message }}</p>
                            </div>
                        </div>
                    </div>
                @enderror

                @error('pendingPhotos.*')
                <div class="rounded-md bg-red-50 border border-red-200 p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-heroicon-s-x-circle class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="error-message text-sm text-red-800">{{ $message }}</p>
                        </div>
                    </div>
                </div>
                @enderror

            {{-- Loading pendant validation automatique --}}
            <div wire:loading wire:target="pendingPhotos" class="my-4 rounded-md bg-blue-50 border border-blue-200 p-3">
                <div class="flex items-center">
                    <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-blue-800">Validation des photos en cours...</span>
                </div>
            </div>
        </div>
    </div>

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

                        {{-- Action Button (coin supérieur droit) --}}
                        <div class="absolute top-2 right-2">
                            <button type="button"
                                    wire:click="removePlaceRequestPhoto({{ $index }})"
                                    title="Retirer cette photo"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md hover:bg-red-50 transition-all group/btn">
                                <svg class="w-4 h-4 text-gray-600 group-hover/btn:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
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

                        {{-- Action Button (coin supérieur droit) --}}
                        <div class="absolute top-2 right-2">
                            <button type="button"
                                    wire:click="removeEditRequestPhoto({{ $index }})"
                                    title="Retirer cette photo"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md hover:bg-red-50 transition-all group/btn">
                                <svg class="w-4 h-4 text-gray-600 group-hover/btn:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
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

                        {{-- Action Button (coin supérieur droit) --}}
                        <div class="absolute top-2 right-2">
                            <button type="button"
                                    wire:click="removePhoto({{ $index }})"
                                    title="Retirer cette photo"
                                    class="p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-white hover:shadow-md hover:bg-red-50 transition-all group/btn">
                                <svg class="w-4 h-4 text-gray-600 group-hover/btn:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
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

    {{-- Info Notice --}}
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <div class="flex flex-col items-center">
            <div class="ml-2 w-full">
                <h3 class="text-sm font-medium text-blue-800 flex items-center gap-2">
                    <svg class="h-6 w-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Conseils pour les photos
                </h3>
                <div class="mt-4 text-sm text-blue-700 w-full">
                    <ul class="list-disc list-inside space-y-3 w-full">
                        <li>Privilégiez des photos de haute qualité et bien cadrées</li>
                        <li>La première photo sera utilisée comme image principale</li>
                        <li>Les miniatures sont générées automatiquement</li>
                        <li>Formats acceptés : JPEG, PNG, WebP (pas de SVG)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
