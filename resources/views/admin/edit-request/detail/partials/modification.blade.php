@props(['editRequest'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-pencil class="h-5 w-5 text-blue-600" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Proposition de modification</h2>
                <p class="text-sm text-gray-600">Sélectionnez les champs à appliquer</p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="px-6 py-5 space-y-4">

        {{-- Instructions ou message de traitement --}}
        @if($editRequest->isAccepted())
            {{-- Message de confirmation si acceptée --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-green-800">
                        <p class="font-medium mb-1">Demande traitée</p>
                        <p>Cette demande de modification a été traitée et validée le {{ $editRequest->processed_at->format('d/m/Y à H:i') }}.</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Instructions si en attente --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Action requise</p>
                        <p>Sélectionnez les champs que vous souhaitez appliquer, puis cliquez sur <strong>"Appliquer les modifications"</strong>. Vous serez redirigé vers le formulaire d'édition avec les valeurs pré-remplies.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Comparaisons des champs proposés --}}
        @php
            $proposedData = $editRequest->proposed_data ?? [];
            $place = $editRequest->place;
            $translation = $place->translate($editRequest->detected_language ?? 'fr');
            $appliedChanges = $editRequest->applied_changes ?? [];
            $appliedFields = $appliedChanges['fields'] ?? [];
        @endphp

        @if(!empty($proposedData))
            <div class="space-y-3">
                @foreach($proposedData as $fieldData)
                    @php
                        // Extraire les données du champ
                        $fieldName = $fieldData['field'];
                        $fieldLabel = $fieldData['field_label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
                        $detectedLang = $fieldData['detected_language'] ?? 'unknown';
                        $hasTranslation = !empty($fieldData['translated_value']);
                        $showTranslation = ($showTranslated[$fieldName] ?? false) && $hasTranslation;

                        // Convertir les valeurs en chaînes si nécessaire
                        $oldValue = $fieldData['old_value'] ?? '';
                        $oldValue = is_array($oldValue) ? json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $oldValue;

                        $newValue = $fieldData['new_value'] ?? '';
                        $newValue = is_array($newValue) ? json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $newValue;

                        $translatedValue = $fieldData['translated_value'] ?? '';
                        $translatedValue = is_array($translatedValue) ? json_encode($translatedValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $translatedValue;

                        // Valeur à afficher (traduction ou original)
                        $displayValue = $showTranslation ? $translatedValue : $newValue;

                        $status = $fieldData['status'] ?? 'pending';
                    @endphp


                    <div wire:key="{{ $fieldName }}" class="border border-gray-200 rounded-lg overflow-hidden">
                        {{-- Header avec checkbox/badge et badge langue --}}
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex items-center justify-between gap-3">
                                @if($editRequest->isAccepted())
                                    {{-- Badge Appliqué / Non retenu si acceptée --}}
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm font-medium text-gray-900">{{ $fieldLabel }}</span>
                                        @if(in_array($fieldName, $appliedFields, true))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <x-heroicon-o-check-circle class="h-3 w-3 mr-1" />
                                                Appliqué
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                                <x-heroicon-o-x-mark class="h-3 w-3 mr-1" />
                                                Non retenu
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    {{-- Checkbox si en attente --}}
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedFields"
                                            value="{{ $fieldName }}"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm font-medium text-gray-900">{{ $fieldLabel }}</span>
                                    </label>
                                @endif

                                {{-- Badge langue détectée --}}
                                @if($detectedLang !== 'fr' && $detectedLang !== 'unknown' && $detectedLang !== 'none')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <x-heroicon-o-language class="h-3 w-3 mr-1" />
                                        {{ strtoupper($detectedLang) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Comparaison --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                            {{-- Valeur actuelle --}}
                            <div class="p-4">
                                <div class="text-xs font-medium text-gray-500 uppercase mb-2">Valeur actuelle</div>
                                <div class="text-sm text-gray-700 bg-gray-50 rounded p-3">
                                    {{ $oldValue ?: '(vide)' }}
                                </div>
                            </div>

                            {{-- Valeur proposée --}}
                            <div class="p-4 bg-blue-50">
                                <div class="text-xs font-medium text-blue-700 uppercase mb-2">Valeur proposée</div>

                                {{-- Mention traduction si affichée --}}
                                @if($showTranslation)
                                    <div class="flex items-center gap-2 mb-2 text-xs text-blue-600">
                                        <x-heroicon-o-language class="h-3.5 w-3.5" />
                                        <span>Traduit depuis {{ strtoupper($detectedLang) }}</span>
                                    </div>
                                @endif

                                <div class="text-sm text-gray-900 bg-white rounded p-3 border border-blue-200 mb-3">
                                    {{ $displayValue ?: '(vide)' }}
                                </div>

                                {{-- Boutons de traduction --}}
                                @if($detectedLang !== 'fr' && $detectedLang !== 'unknown' && $detectedLang !== 'none')
                                    <div class="flex items-center gap-2">
                                        @if(!$hasTranslation)
                                            {{-- Bouton traduire --}}
                                            <button
                                                wire:click="translateField('{{ $fieldName }}')"
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded hover:bg-blue-100 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                                <x-heroicon-o-language class="h-3.5 w-3.5" />
                                                <span wire:loading.remove wire:target="translateField('{{ $fieldName }}')">Traduire</span>
                                                <span wire:loading wire:target="translateField('{{ $fieldName }}')">...</span>
                                            </button>
                                        @else
                                            {{-- Bouton switcher --}}
                                            <button
                                                wire:click="toggleFieldTranslation('{{ $fieldName }}')"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors">
                                                <x-heroicon-o-arrow-path class="h-3.5 w-3.5" />
                                                @if($showTranslation)
                                                    Original
                                                @else
                                                    Traduction
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <x-heroicon-o-exclamation-triangle class="h-12 w-12 mx-auto text-gray-400 mb-3" />
                <p>Aucune modification proposée.</p>
            </div>
        @endif

        {{-- Contact email --}}
        @if($editRequest->contact_email)
            <div class="pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Contact</h3>
                <div class="flex items-center gap-2">
                    <x-heroicon-o-envelope class="h-4 w-4 text-gray-400" />
                    <a href="mailto:{{ $editRequest->contact_email }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        {{ $editRequest->contact_email }}
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
