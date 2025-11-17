{{-- Modal Traduction Photo --}}
@if ($showPhotoTranslationModal)
    <div class="fixed inset-0 z-40" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-white/75 backdrop-blur-xs transition-opacity"
             @click="$wire.closePhotoTranslationModal()"
             aria-hidden="true">
        </div>

        {{-- Modal panel avec scroll --}}
        <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl z-10">
            {{-- Header --}}
            <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-200 sticky top-0 z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">
                                Texte SEO de la photo
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Définissez le texte alternatif (balise alt) pour cette photo en français et anglais.
                            </p>
                        </div>
                        <button type="button"
                                wire:click="closePhotoTranslationModal"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

            {{-- Body --}}
            <div class="bg-white px-6 py-4">
                {{-- Photo preview avec ratio carré --}}
                @if ($currentPhotoPreviewUrl)
                    <div class="mb-6">
                        <div class="w-full max-w-[10em] aspect-16/10 mx-auto rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-200">
                            <img src="{{ $currentPhotoPreviewUrl }}"
                                 alt="Aperçu de la photo"
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

                    {{-- Onglets FR/EN --}}
                    <div x-data="{ activeTab: 'fr' }"
                         x-on:switch-photo-translation-tab.window="activeTab = $event.detail.locale"
                         class="space-y-4">
                        {{-- Tabs navigation --}}
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button type="button"
                                        @click="activeTab = 'fr'"
                                        :class="{
                                            'border-blue-500 text-blue-600': activeTab === 'fr',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'fr'
                                        }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    🇫🇷 Français
                                </button>
                                <button type="button"
                                        @click="activeTab = 'en'"
                                        :class="{
                                            'border-blue-500 text-blue-600': activeTab === 'en',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'en'
                                        }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    🇬🇧 Anglais
                                </button>
                            </nav>
                        </div>

                        {{-- FR Tab Content --}}
                        <div x-show="activeTab === 'fr'" class="space-y-4">
                            <div>
                                <label for="alt_text_fr" class="block text-sm font-medium text-gray-700 mb-2">
                                    Texte alternatif (FR)
                                </label>
                                <input type="text"
                                       id="alt_text_fr"
                                       wire:model="currentPhotoTranslations.fr.alt_text"
                                       maxlength="125"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Ex: Vue extérieure du Centre spatial Kennedy">
                                <p class="mt-1 text-xs text-gray-500">
                                    Maximum 125 caractères. Si vide, utilisera "{{ isset($translations['fr']['title']) ? $translations['fr']['title'] : 'Titre du lieu' }} - Image XX" par défaut.
                                </p>
                                @error('currentPhotoTranslations.fr.alt_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Bouton traduire vers EN --}}
                            <div class="flex justify-end">
                                <button type="button"
                                        wire:click="translatePhotoAltText('fr')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                    </svg>
                                    Traduire vers EN →
                                </button>
                            </div>
                        </div>

                        {{-- EN Tab Content --}}
                        <div x-show="activeTab === 'en'" class="space-y-4">
                            <div>
                                <label for="alt_text_en" class="block text-sm font-medium text-gray-700 mb-2">
                                    Alternative text (EN)
                                </label>
                                <input type="text"
                                       id="alt_text_en"
                                       wire:model="currentPhotoTranslations.en.alt_text"
                                       maxlength="125"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Ex: Exterior view of Kennedy Space Center">
                                <p class="mt-1 text-xs text-gray-500">
                                    Maximum 125 characters. If empty, will use "{{ isset($translations['en']['title']) ? $translations['en']['title'] : 'Place title' }} - Image XX" by default.
                                </p>
                                @error('currentPhotoTranslations.en.alt_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Bouton traduire vers FR --}}
                            <div class="flex justify-start">
                                <button type="button"
                                        wire:click="translatePhotoAltText('en')"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    ← Traduire vers FR
                                    <svg class="ml-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 sticky bottom-0">
                <button type="button"
                        wire:click="closePhotoTranslationModal"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </button>
                <button type="button"
                        wire:click="savePhotoTranslations"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
@endif
