{{-- Delete Confirmation Modal --}}
<div x-show="$wire.showDeleteModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-500/50 transition-opacity"
         @click="$wire.cancelDelete()"></div>

    {{-- Modal container --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full max-w-lg">

            {{-- Modal content --}}
            <div class="bg-white px-6 py-5">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer le tag
                        </h3>
                        <div class="mt-3 space-y-3">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer ce tag ? Cette action est irréversible.
                            </p>

                            @if($associatedPlacesCount && $associatedPlacesCount > 0)
                                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-amber-400" />
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-amber-800">
                                                Attention - Impact sur les lieux
                                            </h3>
                                            <div class="mt-2 text-sm text-amber-700">
                                                <p>
                                                    Ce tag est actuellement associé à <strong>{{ $associatedPlacesCount }}</strong> lieu{{ $associatedPlacesCount > 1 ? 'x' : '' }}.
                                                </p>
                                                <p class="mt-2">
                                                    ⚠️ <strong>Impact important :</strong> En supprimant ce tag, les lieux concernés ne seront plus visibles par les visiteurs en mode "Monde entier", car ce mode nécessite au moins un tag pour afficher un lieu.
                                                </p>
                                                <p class="mt-2">
                                                    Le tag sera automatiquement dissocié de tous ces lieux lors de la suppression.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal actions --}}
            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                {{-- Delete button --}}
                <button type="button"
                        wire:click="delete"
                        wire:loading.attr="disabled"
                        class="inline-flex justify-center items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove.flex class="flex gap-2 items-center">
                        <x-heroicon-o-trash class="w-4 h-4" />
                        Supprimer définitivement
                    </span>
                    <span wire:loading.flex class="flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Suppression...
                    </span>
                </button>

                {{-- Cancel button --}}
                <button type="button"
                        wire:click="cancelDelete"
                        class="inline-flex justify-center items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors duration-150">
                    Annuler
                </button>
            </div>

        </div>
    </div>
</div>
