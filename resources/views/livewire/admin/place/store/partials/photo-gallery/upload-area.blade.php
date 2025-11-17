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
