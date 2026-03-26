<div wire:poll.5s>
    @if ($workflows->isNotEmpty())
        <div class="space-y-2">
            @foreach ($workflows as $workflow)
                <div wire:key="history-{{ $workflow->id }}"
                     class="group flex items-center justify-between rounded-2xl border border-gray-100 bg-white px-5 py-4 transition-colors hover:bg-gray-50">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            <p class="truncate text-sm font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($workflow->query, 60) }}</p>
                            <span class="inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-[11px] font-medium
                                @switch($workflow->state)
                                    @case(\App\Enums\AutofillWorkflowState::Completed)
                                        bg-green-100 text-green-700
                                        @break
                                    @case(\App\Enums\AutofillWorkflowState::Paused)
                                        bg-red-100 text-red-600
                                        @break
                                    @case(\App\Enums\AutofillWorkflowState::Abandoned)
                                        bg-gray-100 text-gray-500
                                        @break
                                    @default
                                        bg-blue-100 text-blue-700
                                @endswitch
                            ">
                                @if ($workflow->isActive())
                                    <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"></span>
                                @endif
                                {{ $workflow->state->label() }}
                            </span>
                            @if ($workflow->isPaused() || $workflow->isActive())
                                <span class="text-[11px] text-gray-400">{{ $workflow->status->label() }}</span>
                            @endif
                        </div>
                        <div class="mt-1 flex items-center gap-3 text-xs text-gray-400">
                            <span>{{ ucfirst($workflow->provider) }}</span>
                            <span class="text-gray-200">·</span>
                            <span>{{ $workflow->created_at->format('d/m/Y H:i') }}</span>
                            <span class="text-gray-200">·</span>
                            <span>{{ $workflow->admin->name ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-3 pl-4">
                        <a href="{{ route('admin.autofill.show', $workflow->id) }}"
                           class="text-sm text-gray-400 transition-colors hover:text-gray-900">
                            Détail
                        </a>
                        @if ($workflow->isDismissed())
                            <button wire:click="deleteWorkflow({{ $workflow->id }})"
                                    wire:confirm="Êtes-vous sûr de vouloir supprimer ce workflow ?"
                                    class="text-sm text-gray-300 transition-colors hover:text-red-500">
                                Supprimer
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $workflows->links() }}
        </div>
    @else
        <div class="py-12 text-center">
            <p class="text-sm text-gray-400">Aucun workflow pour le moment.</p>
        </div>
    @endif
</div>
