@props(['editRequest'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-orange-600" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Signalement simple</h2>
                <p class="text-sm text-gray-600">Un utilisateur a signalé un problème concernant ce lieu</p>
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
                        <p class="font-medium mb-1">Signalement traité</p>
                        <p>Ce signalement a été traité et marqué comme résolu le {{ $editRequest->processed_at->format('d/m/Y à H:i') }}.</p>
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
                        <p>Après avoir lu ce signalement et effectué les modifications nécessaires manuellement sur le lieu, cliquez sur <strong>"Marquer comme traité"</strong> pour confirmer la prise en compte.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Détails du signalement --}}
        <div class="my-12">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-900">Détails du signalement</h3>

                {{-- Badge langue détectée --}}
                @if($editRequest->detected_language && $editRequest->detected_language !== 'unknown')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <x-heroicon-o-language class="h-3 w-3 mr-1" />
                        Langue : {{ strtoupper($editRequest->detected_language) }}
                    </span>
                @endif
            </div>

            <div class="bg-gray-50 rounded-lg p-4 py-6 border border-gray-200">
                @php
                    $hasTranslation = !empty($editRequest->description_translation);
                    $showTranslation = $showDescriptionTranslated && $hasTranslation;
                    $displayText = $showTranslation
                        ? $editRequest->description_translation
                        : ($editRequest->details ?? 'Aucun détail fourni.');
                @endphp

                {{-- Mention traduction si affichée --}}
                @if($showTranslation)
                    <div class="flex items-center gap-2 mb-3 text-xs text-blue-600">
                        <x-heroicon-o-language class="h-3.5 w-3.5" />
                        <span>Traduit depuis {{ strtoupper($editRequest->detected_language) }}</span>
                    </div>
                @endif

                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $displayText }}</p>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex items-center gap-2 mt-3">
                @if($editRequest->detected_language !== 'fr' && $editRequest->detected_language !== 'unknown')
                    @if(!$hasTranslation)
                        {{-- Bouton traduire --}}
                        <button
                            wire:click="translateDescription"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-300 rounded-md hover:bg-blue-100 hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <x-heroicon-o-language class="h-4 w-4" />
                            <span wire:loading.remove wire:target="translateDescription">Traduire en français</span>
                            <span wire:loading wire:target="translateDescription">Traduction...</span>
                        </button>
                    @else
                        {{-- Bouton switcher --}}
                        <button
                            wire:click="toggleDescriptionTranslation"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors">
                            <x-heroicon-o-arrow-path class="h-4 w-4" />
                            @if($showDescriptionTranslated)
                                Afficher l'original
                            @else
                                Afficher la traduction
                            @endif
                        </button>
                    @endif
                @endif
            </div>
        </div>

        {{-- Contact email --}}
        @if($editRequest->contact_email)
            <div>
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
