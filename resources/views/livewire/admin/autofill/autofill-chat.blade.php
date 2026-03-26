<div>
    {{-- Polling conditionnel --}}
    @if ($isPolling)
        <div wire:poll.2s="refreshMessages"></div>
    @endif

    {{-- Message d'accueil si pas de workflow et pas de messages --}}
    @if (! $activeWorkflowId && $messages->isEmpty())
        <div class="py-16 text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold tracking-tight text-gray-900">Assistant de remplissage</h3>
            <p class="mx-auto mt-3 max-w-md text-[15px] leading-relaxed text-gray-400">Décrivez le type de lieux que vous recherchez et l'IA se chargera de les trouver, enrichir et créer automatiquement.</p>
        </div>
    @else
        {{-- Statut subtil en haut --}}
        @if ($this->activeWorkflow)
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block h-2 w-2 rounded-full
                        @if ($this->activeWorkflow->isDismissed())
                            bg-gray-400
                        @elseif ($this->activeWorkflow->isPaused())
                            bg-red-500
                        @elseif ($this->activeWorkflow->status->isAwaiting())
                            bg-amber-500
                        @else
                            bg-gray-900 animate-pulse
                        @endif
                    "></span>
                    <span class="text-sm text-gray-400">
                        {{ $this->activeWorkflow->state->label() }}
                        @if ($this->activeWorkflow->isPaused())
                            <span class="mx-1 text-gray-200">·</span>
                            {{ $this->activeWorkflow->status->label() }}
                        @endif
                        <span class="mx-1 text-gray-200">/</span>
                        {{ $this->activeWorkflow->query }}
                    </span>
                </div>

                @if ($this->activeWorkflow->isActive())
                    <button wire:click="abandonWorkflow"
                            wire:confirm="Êtes-vous sûr de vouloir abandonner ce workflow ?"
                            class="cursor-pointer text-sm text-gray-400 transition-colors hover:text-red-500">
                        Abandonner
                    </button>
                @elseif ($this->activeWorkflow->isPaused())
                    <button wire:click="abandonWorkflow"
                            wire:confirm="Êtes-vous sûr de vouloir abandonner ce workflow ?"
                            class="cursor-pointer text-sm text-gray-400 transition-colors hover:text-red-500">
                        Abandonner
                    </button>
                @endif
            </div>
        @endif

        {{-- Blocage par un autre admin --}}
        @if ($blockedByOtherAdmin)
            <div class="my-4 rounded-2xl bg-gray-50 px-5 py-4">
                <p class="text-sm text-gray-500">
                    Un workflow est en cours par <strong class="text-gray-700">{{ $blockedByAdminName }}</strong>. Veuillez patienter.
                </p>
            </div>
        @endif

        {{-- Messages --}}
        <div class="space-y-5">
            @foreach ($messages as $message)
                <div x-data x-transition.opacity.duration.300ms wire:key="msg-{{ $message->id }}">
                    @switch($message->type)
                        @case(\App\Enums\AutofillMessageType::Text)
                            @include('livewire.admin.autofill.partials.message-text', ['message' => $message])
                            @break
                        @case(\App\Enums\AutofillMessageType::Progress)
                            @include('livewire.admin.autofill.partials.message-progress', ['message' => $message])
                            @break
                        @case(\App\Enums\AutofillMessageType::Selection)
                            @include('livewire.admin.autofill.partials.message-selection', ['message' => $message])
                            @break
                        @case(\App\Enums\AutofillMessageType::Images)
                            @include('livewire.admin.autofill.partials.message-images', ['message' => $message])
                            @break
                        @case(\App\Enums\AutofillMessageType::Recap)
                            @include('livewire.admin.autofill.partials.message-recap', ['message' => $message])
                            @break
                        @case(\App\Enums\AutofillMessageType::Error)
                            @include('livewire.admin.autofill.partials.message-error', ['message' => $message])
                            @break
                    @endswitch
                </div>
            @endforeach
        </div>
    @endif

    {{-- Barre de saisie --}}
    <div class="mt-8 pb-6">
        {{-- Modale de confirmation d'interruption --}}
        @if ($showInterruptConfirm)
            <div class="mb-4 rounded-2xl bg-gray-50 px-5 py-4">
                <p class="text-sm text-gray-600">Vous avez déjà un workflow en cours. Voulez-vous l'abandonner pour en lancer un nouveau ?</p>
                <div class="mt-3 flex gap-3">
                    <button wire:click="confirmInterrupt" class="cursor-pointer rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800">
                        Abandonner et relancer
                    </button>
                    <button wire:click="cancelInterrupt" class="cursor-pointer rounded-full px-4 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700">
                        Annuler
                    </button>
                </div>
            </div>
        @endif

        {{-- Modale de confirmation sélection vide --}}
        @if ($showEmptySelectionConfirm)
            <div class="mb-4 rounded-2xl bg-gray-50 px-5 py-4">
                <p class="text-sm text-gray-600">Aucun lieu sélectionné. Êtes-vous sûr de vouloir terminer le workflow ?</p>
                <div class="mt-3 flex gap-3">
                    <button wire:click="confirmEmptySelection" class="cursor-pointer rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800">
                        Terminer le workflow
                    </button>
                    <button wire:click="cancelEmptySelection" class="cursor-pointer rounded-full px-4 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-gray-700">
                        Annuler
                    </button>
                </div>
            </div>
        @endif

        @if (! $activeWorkflowId)
            <form wire:submit="startWorkflow">
                {{-- Options (provider + quantité) --}}
                <div class="mb-3 flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label for="provider" class="text-xs text-gray-400">Modèle</label>
                        <select wire:model="provider" id="provider"
                                class="appearance-none rounded-lg border-0 bg-gray-50 py-1.5 pl-3 pr-8 text-xs text-gray-600 focus:ring-1 focus:ring-gray-300">
                            @foreach ($this->availableProviders as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="quantity" class="text-xs text-gray-400">Quantité</label>
                        <input wire:model="quantity" type="number" id="quantity" min="1" max="{{ config('autofill.max_quantity', 50) }}"
                               class="w-16 rounded-lg border-0 bg-gray-50 py-1.5 text-center text-xs text-gray-600 focus:ring-1 focus:ring-gray-300">
                    </div>
                </div>

                {{-- Input principal --}}
                <div class="relative">
                    <input wire:model="query"
                           type="text"
                           id="query"
                           placeholder="Décrivez les lieux que vous recherchez..."
                           class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-4 pl-5 pr-14 text-[15px] text-gray-900 placeholder-gray-400 transition-colors focus:border-gray-300 focus:bg-white focus:outline-none focus:ring-0"
                           autocomplete="off">

                    <button type="submit"
                            class="absolute right-2.5 top-1/2 flex h-9 w-9 -translate-y-1/2 cursor-pointer items-center justify-center rounded-xl bg-gray-900 text-white transition-colors hover:bg-gray-800 disabled:opacity-30"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-30"
                            wire:target="startWorkflow">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
                        </svg>
                    </button>
                </div>

                @error('query')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('provider')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('quantity')
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </form>
        @elseif ($blockedByOtherAdmin)
            <div class="py-3 text-center text-sm text-gray-300">
                Interface désactivée — un workflow est en cours par un autre administrateur.
            </div>
        @elseif ($this->activeWorkflow && ($this->activeWorkflow->status->isAwaiting() || $this->activeWorkflow->isPaused()))
            {{-- Checkpoint or paused: user interacts via messages (Resume/Abandon buttons) --}}
        @else
            <div class="py-3 text-center text-sm text-gray-300">
                Workflow en cours — veuillez patienter ou abandonner le workflow.
            </div>
        @endif
    </div>
</div>
