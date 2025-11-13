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
                    <x-heroicon-o-check-circle class="h-5 w-5" />
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
                    <x-heroicon-o-question-mark-circle class="h-5 w-5" />
                    <span>
                        Langue d'origine non détectée
                    </span>
                </div>

            @elseif ($showSpecialTranslateButton)
                {{-- Scénario 2: Autre langue (avec bouton traduction) --}}
                <div class="flex flex-wrap gap-4 items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-blue-700">
                        <x-heroicon-o-language class="h-5 w-5" />
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
                            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                            <span>Traduction en cours...</span>
                        </span>
                    </button>
                </div>

                {{-- Message d'erreur si échec --}}
                @error('translation')
                    <div class="mt-2 flex items-center space-x-2 text-sm text-red-600">
                        <x-heroicon-o-exclamation-circle class="h-4 w-4" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror

            @else
                {{-- Scénario 1: FR ou EN détecté --}}
                <div class="flex items-center space-x-2 text-sm text-blue-700">
                    <x-heroicon-o-language class="h-5 w-5" />
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
                @php
                    // Vérifier si cet onglet contient des champs modifiés
                    $hasModifiedFields = false;
                    foreach (['title', 'description', 'practical_info'] as $field) {
                        if (isset($fieldLanguages[$field]) && in_array("translations.{$locale}.{$field}", $highlightedFields)) {
                            $hasModifiedFields = true;
                            break;
                        }
                    }
                @endphp
                <button type="button"
                        @click="activeTab = '{{ $locale }}'"
                        :class="{
                            'border-blue-500 text-blue-600': activeTab === '{{ $locale }}',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $locale }}'
                        }"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-150 inline-flex items-center gap-2">
                    {{ strtoupper($locale) }}

                    {{-- Badge warning pour champs modifiés --}}
                    @if($hasModifiedFields)
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-amber-500" />
                    @endif

                    {{-- Badge erreur validation --}}
                    @if ($errors->has("translations.{$locale}.*"))
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
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
            <div class="flex justify-center">
                <button type="button"
                        wire:click="initiateTranslation('{{ $locale }}')"
                        wire:loading.attr="disabled"
                        wire:target="initiateTranslation"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed">

                    {{-- Icon normal --}}
                    <span wire:loading.remove wire:target="initiateTranslation">
                        <x-heroicon-o-language class="w-4 h-4" />
                    </span>

                    {{-- Loading spinner --}}
                    <span wire:loading wire:target="initiateTranslation">
                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                    </span>

                    {{-- Text --}}
                    <span wire:loading.remove wire:target="initiateTranslation">Traduire vers <b>{{ $locale === 'fr' ? 'EN' : 'FR' }}</b></span>
                    <span wire:loading wire:target="initiateTranslation">Traduction en cours...</span>
                </button>
            </div>

            {{-- Title --}}
            @php
                $titleFieldKey = "translations.{$locale}.title";
                $isHighlighted = in_array($titleFieldKey, $highlightedFields);
                $oldTitleValue = $oldValues[$titleFieldKey] ?? null;
                $titleDetectedLang = $fieldLanguages['title'] ?? null;
                $titleTranslatedFrom = $fieldTranslatedFrom['title'] ?? null;
                $showTranslateButton = $locale === 'fr' && $titleDetectedLang && !in_array($titleDetectedLang, ['fr', 'en', 'unknown', 'none']) && !$titleTranslatedFrom;

                // Mapping des langues vers drapeaux
                $languageFlags = [
                    'en' => '🇬🇧',
                    'fr' => '🇫🇷',
                    'es' => '🇪🇸',
                    'de' => '🇩🇪',
                    'it' => '🇮🇹',
                    'pt' => '🇵🇹',
                    'pl' => '🇵🇱',
                    'nl' => '🇳🇱',
                    'ru' => '🇷🇺',
                    'ja' => '🇯🇵',
                    'zh' => '🇨🇳',
                ];

                // Mapping des noms de langues
                $languageNames = config('translation.providers.'.config('translation.default_provider').'.language_names', []);
            @endphp

            {{-- Badge indicateur de langue + bouton traduction --}}
            @if($isHighlighted && $titleDetectedLang)
                <div class="mb-2 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        {{-- Badge "Modifié" --}}
                        <span class="inline-flex items-centers gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <x-heroicon-o-information-circle class="w-3 h-3" />
                            <span>Modifié</span>
                        </span>

                        {{-- Badge langue détectée --}}
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <span>{{ $languageFlags[$titleDetectedLang] ?? '🌐' }}</span>
                            <span>{{ $languageNames[$titleDetectedLang] ?? ucfirst($titleDetectedLang) }}</span>
                        </span>

                        {{-- Badge "Traduit depuis" si applicable --}}
                        @if($titleTranslatedFrom)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-heroicon-o-check-circle class="w-3 h-3" />
                                <span>Traduit depuis {{ $languageNames[$titleTranslatedFrom] ?? ucfirst($titleTranslatedFrom) }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Bouton traduction pour langues "autres" --}}
                    @if($showTranslateButton)
                        <button type="button"
                                wire:click="translateFieldFromSource('title')"
                                wire:loading.attr="disabled"
                                wire:target="translateFieldFromSource"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="translateFieldFromSource">
                                <x-heroicon-o-language class="w-3.5 h-3.5" />
                            </span>
                            <span wire:loading wire:target="translateFieldFromSource">
                                <x-heroicon-o-arrow-path class="animate-spin w-3.5 h-3.5" />
                            </span>
                            <span wire:loading.remove wire:target="translateFieldFromSource">Traduire vers FR</span>
                            <span wire:loading wire:target="translateFieldFromSource">Traduction...</span>
                        </button>
                    @endif
                </div>
            @endif

            <x-admin.form.input
                label="Titre"
                name="translations_{{ $locale }}_title"
                wire:model.live.debounce.500ms="translations.{{ $locale }}.title"
                :required="true"
                :error="$errors->first('translations.' . $locale . '.title')"
            />

            {{-- Ancienne valeur (en dessous du champ) --}}
            @if($isHighlighted && $oldTitleValue)
                <div class="mt-[-0.5em] mb-8 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <p class="text-xs font-medium text-amber-800">Ancienne valeur :</p>
                            <p class="text-sm text-amber-700 mt-1">{{ $oldTitleValue }}</p>
                        </div>
                    </div>
                </div>
            @endif

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
            @php
                $descFieldKey = "translations.{$locale}.description";
                $isDescHighlighted = in_array($descFieldKey, $highlightedFields);
                $oldDescValue = $oldValues[$descFieldKey] ?? null;
                $descDetectedLang = $fieldLanguages['description'] ?? null;
                $descTranslatedFrom = $fieldTranslatedFrom['description'] ?? null;
                $showDescTranslateButton = $locale === 'fr' && $descDetectedLang && !in_array($descDetectedLang, ['fr', 'en', 'unknown', 'none']) && !$descTranslatedFrom;
            @endphp

            {{-- Badge indicateur de langue + bouton traduction --}}
            @if($isDescHighlighted && $descDetectedLang)
                <div class="mb-2 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        {{-- Badge "Modifié" --}}
                        <span class="inline-flex items-centers gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <x-heroicon-o-information-circle class="w-3 h-3" />
                            <span>Modifié</span>
                        </span>

                        {{-- Badge langue détectée --}}
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <span>{{ $languageFlags[$descDetectedLang] ?? '🌐' }}</span>
                            <span>{{ $languageNames[$descDetectedLang] ?? ucfirst($descDetectedLang) }}</span>
                        </span>

                        {{-- Badge "Traduit depuis" si applicable --}}
                        @if($descTranslatedFrom)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-heroicon-o-check-circle class="w-3 h-3" />
                                <span>Traduit depuis {{ $languageNames[$descTranslatedFrom] ?? ucfirst($descTranslatedFrom) }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Bouton traduction pour langues "autres" --}}
                    @if($showDescTranslateButton)
                        <button type="button"
                                wire:click="translateFieldFromSource('description')"
                                wire:loading.attr="disabled"
                                wire:target="translateFieldFromSource"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="translateFieldFromSource">
                                <x-heroicon-o-language class="w-3.5 h-3.5" />
                            </span>
                            <span wire:loading wire:target="translateFieldFromSource">
                                <x-heroicon-o-arrow-path class="animate-spin w-3.5 h-3.5" />
                            </span>
                            <span wire:loading.remove wire:target="translateFieldFromSource">Traduire vers FR</span>
                            <span wire:loading wire:target="translateFieldFromSource">Traduction...</span>
                        </button>
                    @endif
                </div>
            @endif

            <x-admin.form.textarea
                label="Description"
                name="translations_{{ $locale }}_description"
                wire:model="translations.{{ $locale }}.description"
                rows="6"
                :required="true"
                :error="$errors->first('translations.' . $locale . '.description')"
            />

            {{-- Ancienne valeur (en dessous du champ) --}}
            @if($isDescHighlighted && $oldDescValue)
                <div class="mt-[-0.5em] mb-8 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <p class="text-xs font-medium text-amber-800">Ancienne valeur :</p>
                            <p class="text-sm text-amber-700 mt-1 whitespace-pre-wrap">{{ $oldDescValue }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Practical Info --}}
            @php
                $practicalFieldKey = "translations.{$locale}.practical_info";
                $isPracticalHighlighted = in_array($practicalFieldKey, $highlightedFields);
                $oldPracticalValue = $oldValues[$practicalFieldKey] ?? null;
                $practicalDetectedLang = $fieldLanguages['practical_info'] ?? null;
                $practicalTranslatedFrom = $fieldTranslatedFrom['practical_info'] ?? null;
                $showPracticalTranslateButton = $locale === 'fr' && $practicalDetectedLang && !in_array($practicalDetectedLang, ['fr', 'en', 'unknown', 'none']) && !$practicalTranslatedFrom;
            @endphp

            {{-- Badge indicateur de langue + bouton traduction --}}
            @if($isPracticalHighlighted && $practicalDetectedLang)
                <div class="mb-2 flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        {{-- Badge "Modifié" --}}
                        <span class="inline-flex items-centers gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <x-heroicon-o-information-circle class="w-3 h-3" />
                            <span>Modifié</span>
                        </span>

                        {{-- Badge langue détectée --}}
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <span>{{ $languageFlags[$practicalDetectedLang] ?? '🌐' }}</span>
                            <span>{{ $languageNames[$practicalDetectedLang] ?? ucfirst($practicalDetectedLang) }}</span>
                        </span>

                        {{-- Badge "Traduit depuis" si applicable --}}
                        @if($practicalTranslatedFrom)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-heroicon-o-check-circle class="w-3 h-3" />
                                <span>Traduit depuis {{ $languageNames[$practicalTranslatedFrom] ?? ucfirst($practicalTranslatedFrom) }}</span>
                            </span>
                        @endif
                    </div>

                    {{-- Bouton traduction pour langues "autres" --}}
                    @if($showPracticalTranslateButton)
                        <button type="button"
                                wire:click="translateFieldFromSource('practical_info')"
                                wire:loading.attr="disabled"
                                wire:target="translateFieldFromSource"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="translateFieldFromSource">
                                <x-heroicon-o-language class="w-3.5 h-3.5" />
                            </span>
                            <span wire:loading wire:target="translateFieldFromSource">
                                <x-heroicon-o-arrow-path class="animate-spin w-3.5 h-3.5" />
                            </span>
                            <span wire:loading.remove wire:target="translateFieldFromSource">Traduire vers FR</span>
                            <span wire:loading wire:target="translateFieldFromSource">Traduction...</span>
                        </button>
                    @endif
                </div>
            @endif

            <x-admin.form.textarea
                label="Informations pratiques"
                name="translations_{{ $locale }}_practical_info"
                wire:model="translations.{{ $locale }}.practical_info"
                rows="4"
                helperText="Horaires, tarifs, accès, etc."
                :error="$errors->first('translations.' . $locale . '.practical_info')"
            />

            {{-- Ancienne valeur (en dessous du champ) --}}
            @if($isPracticalHighlighted && $oldPracticalValue)
                <div class="mt-[-0.5em] mb-8 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <p class="text-xs font-medium text-amber-800">Ancienne valeur :</p>
                            <p class="text-sm text-amber-700 mt-1 whitespace-pre-wrap">{{ $oldPracticalValue }}</p>
                        </div>
                    </div>
                </div>
            @endif

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
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-check-circle class="h-6 w-6 text-blue-600" />
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                Sélectionner les champs à remplacer
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-500">
                                    Les champs suivants contiennent déjà du contenu. Sélectionnez ceux que vous souhaitez remplacer :
                                </p>
                                <div class="mt-4 space-y-3">
                                    @foreach($fieldsToOverwrite as $field)
                                        @php
                                            $fieldLabels = [
                                                'title' => 'Titre',
                                                'description' => 'Description',
                                                'practical_info' => 'Informations pratiques',
                                            ];
                                        @endphp
                                        <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox"
                                                   wire:model.live="selectedFieldsToOverwrite"
                                                   value="{{ $field }}"
                                                   class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ $fieldLabels[$field] ?? ucfirst($field) }}
                                                </span>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    Le contenu actuel sera remplacé par la traduction
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-4 text-sm text-gray-500">
                                    <x-heroicon-o-check-circle class="inline h-4 w-4 text-green-600 mr-1" />
                                    Les champs vides seront automatiquement traduits
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal actions --}}
                <div class="bg-gray-50 px-6 py-4 flex flex-col gap-3">
                    {{-- Replace selection button --}}
                    <button type="button"
                            wire:click="confirmTranslation"
                            :disabled="$wire.selectedFieldsToOverwrite.length === 0"
                            :class="{
                                'opacity-50 cursor-not-allowed': $wire.selectedFieldsToOverwrite.length === 0,
                                'hover:bg-indigo-500': $wire.selectedFieldsToOverwrite.length > 0
                            }"
                            title="Remplacer les champs sélectionnés + traduire les champs vides"
                            class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-150">
                        <x-heroicon-o-check-circle class="w-4 h-4" />
                        Remplacer la sélection
                    </button>

                    {{-- Only empty fields button (only if there are empty fields) --}}
                    @if($hasEmptyFieldsToTranslate)
                        <button type="button"
                                wire:click="confirmTranslationOnlyEmpty"
                                title="Traduire uniquement les champs vides, conserver tous les champs déjà remplis"
                                class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-150">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Champs vides uniquement
                        </button>
                    @endif

                    {{-- Cancel button --}}
                    <button type="button"
                            wire:click="cancelTranslation"
                            class="inline-flex w-full justify-center items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-150">
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
