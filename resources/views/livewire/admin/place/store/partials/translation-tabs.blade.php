@php
    $supportedLocales = config('locales.supported', ['fr', 'en']);
@endphp

<div class="border-t border-gray-200">

    {{-- 🆕 BANDEAU DÉTECTION LANGUE (uniquement si PlaceRequest) --}}
    @if ($mode === 'create' && $placeRequestId)
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            @if ($isTranslatedFromSource)
                {{-- Après traduction --}}
                <div class="flex items-center space-x-2 text-sm text-green-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">
                        Traduit depuis {{ $detectedLanguageName }}
                    </span>
                </div>

                {{-- Message flash succès --}}
                @if (session()->has('translation_success'))
                    <div class="mt-2 text-sm text-green-600">
                        {{ session('translation_success') }}
                    </div>
                @endif

            @elseif (!$detectedLanguage || $detectedLanguage === 'unknown')
                {{-- Scénario 3: Unknown --}}
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        Langue d'origine non détectée
                    </span>
                </div>

            @elseif ($showSpecialTranslateButton)
                {{-- Scénario 2: Autre langue (avec bouton traduction) --}}
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-blue-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                        <span class="font-medium">
                            Langue détectée : {{ $detectedLanguageName }}
                        </span>
                    </div>

                    <button
                        type="button"
                        wire:click="translateFromSourceLanguage"
                        wire:loading.attr="disabled"
                        wire:target="translateFromSourceLanguage"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">

                        <span wire:loading.remove wire:target="translateFromSourceLanguage">
                            Traduire depuis {{ $detectedLanguageName }} vers Français
                        </span>

                        <span wire:loading wire:target="translateFromSourceLanguage" class="flex items-center space-x-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Traduction en cours...</span>
                        </span>
                    </button>
                </div>

                {{-- Message d'erreur si échec --}}
                @error('translation')
                    <div class="mt-2 flex items-center space-x-2 text-sm text-red-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

            @else
                {{-- Scénario 1: FR ou EN détecté --}}
                <div class="flex items-center space-x-2 text-sm text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    <span class="font-medium">
                        Langue détectée : {{ $detectedLanguageName }}
                    </span>
                </div>
            @endif
        </div>
    @endif

    {{-- Tab Headers --}}
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex -mb-px px-6" aria-label="Tabs">
            @foreach ($supportedLocales as $locale)
                <button type="button"
                        @click="activeTab = '{{ $locale }}'"
                        :class="{
                            'border-blue-500 text-blue-600': activeTab === '{{ $locale }}',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $locale }}'
                        }"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-150">
                    {{ strtoupper($locale) }}
                    @if ($errors->has("translations.{$locale}.*"))
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                            !
                        </span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab Contents --}}
    @foreach ($supportedLocales as $locale)
        <div x-show="activeTab === '{{ $locale }}'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="p-6 space-y-6">

            {{-- Auto-translate button --}}
            <div class="flex justify-end">
                <button type="button"
                        wire:click="initiateTranslation('{{ $locale }}')"
                        wire:loading.attr="disabled"
                        wire:target="initiateTranslation"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed">

                    {{-- Icon normal --}}
                    <span wire:loading.remove wire:target="initiateTranslation">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </span>

                    {{-- Loading spinner --}}
                    <span wire:loading wire:target="initiateTranslation">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>

                    {{-- Text --}}
                    <span wire:loading.remove wire:target="initiateTranslation">Traduire depuis <b>{{ $locale === 'fr' ? 'EN' : 'FR' }}</b></span>
                    <span wire:loading wire:target="initiateTranslation">Traduction en cours...</span>
                </button>
            </div>

            {{-- Title --}}
            <x-admin.form.input
                label="Titre"
                name="translations_{{ $locale }}_title"
                wire:model.live.debounce.500ms="translations.{{ $locale }}.title"
                :required="true"
                :error="$errors->first('translations.' . $locale . '.title')"
            />

            {{-- Slug --}}
            <x-admin.form.input
                label="Slug"
                name="translations_{{ $locale }}_slug"
                wire:model="translations.{{ $locale }}.slug"
                :required="true"
                helperText="Format: lettres minuscules, chiffres et tirets uniquement"
                :error="$errors->first('translations.' . $locale . '.slug')"
            />

            {{-- Description --}}
            <x-admin.form.textarea
                label="Description"
                name="translations_{{ $locale }}_description"
                wire:model="translations.{{ $locale }}.description"
                rows="6"
                :required="true"
                :error="$errors->first('translations.' . $locale . '.description')"
            />

            {{-- Practical Info --}}
            <x-admin.form.textarea
                label="Informations pratiques"
                name="translations_{{ $locale }}_practical_info"
                wire:model="translations.{{ $locale }}.practical_info"
                rows="4"
                helperText="Horaires, tarifs, accès, etc."
                :error="$errors->first('translations.' . $locale . '.practical_info')"
            />

            {{-- Status - Désactivé : toutes les traductions sont automatiquement publiées --}}
            {{--
            <x-admin.form.select
                label="Statut de publication"
                name="translations_{{ $locale }}_status"
                wire:model="translations.{{ $locale }}.status"
                :error="$errors->first('translations.' . $locale . '.status')"
            >
                <option value="draft">Brouillon</option>
                <option value="published">Publié</option>
            </x-admin.form.select>
            --}}
        </div>
    @endforeach

    {{-- Confirmation modal for overwriting existing translations --}}
    <div x-show="$wire.showTranslationConfirmation"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-500/50 transition-opacity"
             @click="$wire.cancelTranslation()"></div>

        {{-- Modal container --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full max-w-lg">

                {{-- Modal content --}}
                <div class="bg-white px-6 py-5">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                Confirmer le remplacement
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-500">
                                    Les champs suivants contiennent déjà du contenu et seront remplacés par la traduction automatique :
                                </p>
                                <ul class="mt-3 space-y-1">
                                    @foreach($fieldsToOverwrite as $field)
                                        @php
                                            $fieldLabels = [
                                                'title' => 'Titre',
                                                'description' => 'Description',
                                                'practical_info' => 'Informations pratiques',
                                            ];
                                        @endphp
                                        <li class="text-sm font-medium text-gray-700">
                                            • {{ $fieldLabels[$field] ?? ucfirst($field) }}
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-3 text-sm text-gray-500">
                                    Voulez-vous continuer ?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal actions --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-col gap-3">
                    {{-- Replace all button --}}
                    <button type="button"
                            wire:click="confirmTranslation"
                            title="Remplacer tous les champs, même ceux déjà remplis"
                            class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Remplacer tout
                    </button>

                    {{-- Translate only empty fields button (only if there are empty fields) --}}
                    @if($hasEmptyFieldsToTranslate)
                        <button type="button"
                                wire:click="confirmTranslationOnlyEmpty"
                                title="Traduire uniquement les champs vides, conserver les champs déjà remplis"
                                class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Champs vides uniquement
                        </button>
                    @endif

                    {{-- Cancel button --}}
                    <button type="button"
                            wire:click="cancelTranslation"
                            class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
