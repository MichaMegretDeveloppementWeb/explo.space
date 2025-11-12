<div>
    {{-- Header sticky --}}
    @include('admin.edit-request.detail.partials.header', ['editRequest' => $editRequest])

    {{-- Content --}}
    <div class="max-w-7xl mx-auto mt-6">
        <div class="space-y-6">
            {{-- Section contexte de la demande (description) --}}
            @include('admin.edit-request.detail.partials.context-section', ['editRequest' => $editRequest])

            {{-- Contenu différent selon le type --}}

            @if($editRequest->isSignalement())
                {{-- Signalement simple : afficher les détails du signalement --}}
                @include('admin.edit-request.detail.partials.signalement', ['editRequest' => $editRequest])
            @endif

            @if($editRequest->isModification())
                {{-- Modification : afficher les champs proposés avec sélection --}}
                @include('admin.edit-request.detail.partials.modification', ['editRequest' => $editRequest])
            @endif

            @if($editRequest->isPhotoSuggestion())
                {{-- Photo suggestion : afficher les photos proposées avec sélection --}}
                @include('admin.edit-request.detail.partials.photo-suggestion', ['editRequest' => $editRequest])
            @endif

            {{-- Traçabilité & Workflow --}}
            @include('admin.edit-request.detail.partials.workflow', ['editRequest' => $editRequest])
        </div>
    </div>

    {{-- Modale de refus - Design moderne SaaS --}}
    @if($showRefusalModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Overlay avec backdrop blur --}}
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300" wire:click="closeRefusalModal"></div>

            {{-- Contenu de la modale --}}
            <div class="relative bg-white rounded-xl shadow-2xl max-w-xl w-full transform transition-all duration-300">
                {{-- Header --}}
                <div class="px-4 py-4 md:px-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-600" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                    Refuser {{ $editRequest->getTypeLabel() }}
                                </h3>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="closeRefusalModal"
                            class="text-gray-400 hover:text-gray-500 transition-colors">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-4 md:px-6 py-5">
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            Êtes-vous certain de vouloir refuser {{ $editRequest->getTypeLabel() }}&nbsp;? Vous pourrez toujours l'accepter ultérieurement si nécessaire.
                        </p>

                        {{-- Textarea pour la raison --}}
                        <div>
                            <label for="refusalReason" class="block text-sm font-medium text-gray-900 mb-2">
                                Raison du refus
                                <span class="text-gray-500 font-normal">(optionnel)</span>
                            </label>
                            <textarea
                                wire:model="refusalReason"
                                id="refusalReason"
                                rows="4"
                                class="block w-full rounded-lg border-gray-300 shadow-md focus:border-red-500 focus:ring-red-500 sm:text-sm transition-colors resize-none p-4"
                                placeholder="Expliquez brièvement pourquoi cette demande ne peut être acceptée..."></textarea>
                            <p class="mt-2 text-xs text-gray-500 flex items-start gap-1.5">
                                <x-heroicon-o-information-circle class="h-4 w-4 flex-shrink-0" />
                                <span>Cette raison sera visible dans l'historique de modération.</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex items-center justify-end gap-3">
                    <button
                        type="button"
                        wire:click="closeRefusalModal"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-200">
                        Annuler
                    </button>
                    <button
                        type="button"
                        wire:click="refuseEditRequest"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm hover:shadow">
                        Confirmer le refus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
