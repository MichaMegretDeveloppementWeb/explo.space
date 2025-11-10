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
                                            'name' => 'Nom',
                                            'description' => 'Description',
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
