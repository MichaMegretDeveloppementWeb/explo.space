<div class="flex justify-start">
    <div class="max-w-[80%] rounded-2xl border border-gray-200 bg-white px-5 py-4">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-500">
                <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="text-[15px] text-gray-700">{{ $message->payload['text'] ?? 'Une erreur est survenue.' }}</p>

                @if ($this->activeWorkflow && $this->activeWorkflow->isPaused())
                    <div class="mt-3 flex gap-2">
                        <button wire:click="resumeWorkflow"
                                class="cursor-pointer rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800">
                            Reprendre
                        </button>
                        <button wire:click="abandonWorkflow"
                                wire:confirm="Êtes-vous sûr de vouloir abandonner ce workflow ?"
                                class="cursor-pointer rounded-full px-4 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-red-500">
                            Abandonner
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
