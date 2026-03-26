<div>
    {{-- Polling conditionnel --}}
    @if ($isPolling)
        <div wire:poll.3s="refreshWorkflow"></div>
    @endif

    @php
        $workflow = $this->workflow;
        $items = $this->items;
        $progress = $this->progress;
    @endphp

    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.autofill.index') }}" class="transition-colors hover:text-gray-900">Remplissage auto</a>
        <svg class="h-3.5 w-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900">Workflow #{{ $workflow->id }}</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-gray-900">{{ $workflow->query }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-400">
                    <span>{{ ucfirst($workflow->provider) }}</span>
                    <span class="text-gray-200">·</span>
                    <span>{{ $workflow->created_at->format('d/m/Y H:i') }}</span>
                    @if ($workflow->totalDuration())
                        <span class="text-gray-200">·</span>
                        <span>{{ gmdate('H:i:s', $workflow->totalDuration()) }}</span>
                    @endif
                    <span class="text-gray-200">·</span>
                    <span>{{ $workflow->admin->name ?? '—' }}</span>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium
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
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"></span>
                @endif
                {{ $workflow->state->label() }}
                @if ($workflow->isPaused() || $workflow->isActive())
                    <span class="mx-1 text-gray-300">·</span>
                    {{ $workflow->status->label() }}
                @endif
            </span>
        </div>

        {{-- Barre de progression --}}
        <div class="mt-8">
            <div class="flex items-center justify-between">
                @foreach ($progress['steps'] as $index => $stepName)
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-medium
                            @if ($index < $progress['currentStep'])
                                bg-gray-900 text-white
                            @elseif ($index === $progress['currentStep'] && ! $progress['isDismissed'] && ! $progress['isPaused'] && ! $progress['isAwaiting'])
                                bg-gray-900 text-white animate-pulse
                            @elseif ($index === $progress['currentStep'] && $progress['isPaused'])
                                bg-red-500 text-white
                            @elseif ($index === $progress['currentStep'] && $progress['isAwaiting'])
                                bg-amber-500 text-white
                            @else
                                bg-gray-100 text-gray-400
                            @endif
                        ">
                            @if ($index < $progress['currentStep'])
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <span class="text-[11px] text-gray-400">{{ $stepName }}</span>
                    </div>
                    @if (! $loop->last)
                        <div class="mx-1 mb-5 h-px grow {{ $index < $progress['currentStep'] ? 'bg-gray-900' : 'bg-gray-100' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div class="mt-8 grid grid-cols-4 gap-3">
            <div class="rounded-2xl bg-gray-50 p-4 text-center">
                <p class="text-2xl font-semibold text-gray-900">{{ $items->count() }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Total</p>
            </div>
            <div class="rounded-2xl bg-gray-50 p-4 text-center">
                <p class="text-2xl font-semibold text-gray-900">{{ $items->where('status', \App\Enums\AutofillItemStatus::Saved)->count() }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Créés</p>
            </div>
            <div class="rounded-2xl bg-gray-50 p-4 text-center">
                <p class="text-2xl font-semibold text-gray-900">{{ $items->where('status', \App\Enums\AutofillItemStatus::Failed)->count() }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Échoués</p>
            </div>
            <div class="rounded-2xl bg-gray-50 p-4 text-center">
                <p class="text-2xl font-semibold text-gray-900">{{ $items->where('status', \App\Enums\AutofillItemStatus::Skipped)->count() }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Ignorés</p>
            </div>
        </div>

        {{-- Tokens & coût --}}
        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-gray-400">
            <span>{{ number_format($workflow->total_tokens_in) }} tokens in</span>
            <span class="text-gray-200">·</span>
            <span>{{ number_format($workflow->total_tokens_out) }} tokens out</span>
            @if ((float) $workflow->total_cost > 0)
                <span class="text-gray-200">·</span>
                <span>${{ number_format((float) $workflow->total_cost, 4) }}</span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex flex-wrap items-center gap-3">
            @if ($workflow->isActive() || $workflow->isPaused())
                <button wire:click="abandonWorkflow"
                        wire:confirm="Êtes-vous sûr de vouloir abandonner ce workflow ?"
                        class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-500 transition-colors hover:border-red-200 hover:text-red-500">
                    Abandonner
                </button>
            @endif

            @if ($workflow->isPaused())
                <button wire:click="resumeWorkflow"
                        class="rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-800">
                    Reprendre
                </button>
            @endif

            <button wire:click="openIoModal"
                    class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-900">
                Voir les entrées/sorties
            </button>

            <a href="{{ route('admin.autofill.index') }}"
               class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-400 transition-colors hover:border-gray-300 hover:text-gray-600">
                Retour
            </a>
        </div>
    </div>

    {{-- Error message --}}
    @if ($workflow->isPaused() && $workflow->error_message)
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white px-5 py-4">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-500">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-900">{{ $workflow->error_message }}</p>
                    @if ($workflow->error_technical)
                        <p class="mt-1 text-xs text-gray-400">{{ $workflow->error_technical }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Liste des items --}}
    @if ($items->isNotEmpty())
        <div class="border-t border-gray-100 pt-8">
            <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900">Lieux ({{ $items->count() }})</h2>

            <div class="space-y-2">
                @foreach ($items as $item)
                    <div wire:key="item-{{ $item->id }}" class="rounded-2xl border border-gray-100 bg-white">
                        {{-- En-tête --}}
                        <button wire:click="toggleItem({{ $item->id }})"
                                class="flex w-full items-center justify-between px-5 py-4 text-left transition-colors hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 text-gray-300 transition-transform {{ in_array($item->id, $expandedItems) ? 'rotate-90' : '' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">{{ $item->name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($item->images_count > 0)
                                    <span class="text-xs text-gray-400">{{ $item->images_count }} image(s)</span>
                                @endif
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium
                                    @switch($item->status)
                                        @case(\App\Enums\AutofillItemStatus::Saved)
                                            bg-gray-900 text-white
                                            @break
                                        @case(\App\Enums\AutofillItemStatus::Failed)
                                            bg-gray-200 text-gray-600
                                            @break
                                        @case(\App\Enums\AutofillItemStatus::Skipped)
                                            bg-gray-100 text-gray-500
                                            @break
                                        @default
                                            bg-gray-900 text-white
                                    @endswitch
                                ">
                                    {{ $item->status->label() }}
                                </span>
                            </div>
                        </button>

                        {{-- Contenu déplié --}}
                        @if (in_array($item->id, $expandedItems))
                            <div class="border-t border-gray-100 px-5 py-4">
                                @php
                                    $enrichment = $item->enrichment_data ?? [];
                                @endphp

                                @if (! empty($enrichment['justification']))
                                    <p class="mb-4 text-sm text-gray-500 italic">{{ $enrichment['justification'] }}</p>
                                @endif

                                @if (! empty($enrichment))
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        @if (! empty($enrichment['title']))
                                            <div>
                                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Titre</p>
                                                <p class="mt-0.5 text-sm text-gray-900">{{ $enrichment['title'] }}</p>
                                            </div>
                                        @endif
                                        @if (! empty($enrichment['address']))
                                            <div>
                                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Adresse</p>
                                                <p class="mt-0.5 text-sm text-gray-900">{{ $enrichment['address'] }}</p>
                                            </div>
                                        @endif
                                        @if (! empty($enrichment['latitude']) && ! empty($enrichment['longitude']))
                                            <div>
                                                <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Coordonnées</p>
                                                <p class="mt-0.5 text-sm text-gray-900">{{ $enrichment['latitude'] }}, {{ $enrichment['longitude'] }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if (! empty($enrichment['description']))
                                        <div class="mt-4">
                                            <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Description</p>
                                            <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ \Illuminate\Support\Str::limit($enrichment['description'], 500) }}</p>
                                        </div>
                                    @endif
                                @endif

                                @if (! empty($item->suggested_tags))
                                    <div class="mt-4">
                                        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Suggestions de tags</p>
                                        <div class="mt-1.5 flex flex-wrap gap-1.5">
                                            @foreach ($item->suggested_tags as $tag)
                                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs text-gray-600">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @php
                                    $placePhotos = $item->place_id ? ($item->place?->photos ?? collect()) : collect();
                                    $images = $item->images_data ?? [];
                                @endphp
                                @if ($placePhotos->isNotEmpty())
                                    <div class="mt-4">
                                        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Images ({{ $placePhotos->count() }})</p>
                                        <div class="mt-1.5 grid grid-cols-4 gap-2 sm:grid-cols-6">
                                            @foreach ($placePhotos as $photo)
                                                <img src="{{ $photo->thumb_url }}" alt="" class="h-16 w-full rounded-lg object-cover" loading="lazy">
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif (! empty($images))
                                    <div class="mt-4">
                                        <p class="text-[11px] font-medium uppercase tracking-wider text-gray-400">Images</p>
                                        <div class="mt-1.5 grid grid-cols-4 gap-2 sm:grid-cols-6">
                                            @foreach ($images as $image)
                                                @if (! empty($image['url']))
                                                    <img src="{{ $image['url'] }}" alt="{{ $image['caption'] ?? '' }}" class="h-16 w-full rounded-lg object-cover" loading="lazy">
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p class="mt-4 text-xs text-gray-300">Aucune image</p>
                                @endif

                                @if ($item->status === \App\Enums\AutofillItemStatus::Failed)
                                    <div class="mt-4 rounded-xl bg-gray-50 p-3">
                                        @if ($item->error_message)
                                            <p class="text-sm text-gray-600">{{ $item->error_message }}</p>
                                        @endif
                                        @if ($item->error_technical)
                                            <p class="mt-1 text-xs text-gray-400">{{ $item->error_technical }}</p>
                                        @endif
                                    </div>
                                @endif

                                @if ($item->place_id)
                                    <div class="mt-4">
                                        <a href="{{ route('admin.places.show', $item->place_id) }}"
                                           class="text-sm font-medium text-gray-900 transition-colors hover:text-gray-600">
                                            Voir la fiche du lieu →
                                        </a>
                                    </div>
                                @endif

                                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-300">
                                    <span>{{ number_format($item->tokens_in) }} in</span>
                                    <span>{{ number_format($item->tokens_out) }} out</span>
                                    @if ((float) $item->cost > 0)
                                        <span>${{ number_format((float) $item->cost, 4) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Modale I/O --}}
    @if ($showIoModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-transition>
            <div class="flex min-h-screen items-center justify-center p-4">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" wire:click="closeIoModal"></div>

                {{-- Contenu modale --}}
                <div class="relative z-10 w-full max-w-3xl rounded-2xl bg-white shadow-2xl">
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Entrées / Sorties</h3>
                        <button wire:click="closeIoModal" class="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Filtres --}}
                    <div class="flex gap-4 border-b border-gray-100 px-6 py-3">
                        <div>
                            <label for="io-filter-step" class="block text-[11px] font-medium uppercase tracking-wider text-gray-400">Étape</label>
                            <select wire:model.live="ioFilterStep" id="io-filter-step" class="mt-1 block w-40 rounded-lg border-0 bg-gray-50 py-1.5 text-sm text-gray-600 focus:ring-1 focus:ring-gray-300">
                                <option value="">Toutes</option>
                                @foreach ($this->availableSteps as $step)
                                    <option value="{{ $step }}">{{ ucfirst($step) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="io-filter-item" class="block text-[11px] font-medium uppercase tracking-wider text-gray-400">Lieu</label>
                            <select wire:model.live="ioFilterItem" id="io-filter-item" class="mt-1 block w-48 rounded-lg border-0 bg-gray-50 py-1.5 text-sm text-gray-600 focus:ring-1 focus:ring-gray-300">
                                <option value="">Tous</option>
                                @foreach ($this->availableItems as $availItem)
                                    <option value="{{ $availItem->id }}">{{ \Illuminate\Support\Str::limit($availItem->name, 30) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Logs --}}
                    <div class="max-h-[60vh] overflow-y-auto px-6 py-4">
                        @php
                            $logs = $this->stepLogs;
                        @endphp

                        @if ($logs->isEmpty())
                            <p class="py-8 text-center text-sm text-gray-300">Aucun log disponible.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($logs as $log)
                                    <div wire:key="log-{{ $log->id }}" class="rounded-xl border border-gray-100 p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2.5">
                                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">{{ ucfirst($log->step) }}</span>
                                                @if ($log->item)
                                                    <span class="text-xs text-gray-400">{{ $log->item->name }}</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3 text-[11px] text-gray-300">
                                                <span>{{ $log->model }}</span>
                                                <span>{{ $log->tokens_in }} in / {{ $log->tokens_out }} out</span>
                                                @if ((float) $log->cost > 0)
                                                    <span>${{ number_format((float) $log->cost, 6) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Input --}}
                                        @if (! empty($log->input_data))
                                            <div class="mt-3" x-data="{ open: false }">
                                                <button @click="open = !open" class="flex items-center gap-1.5 text-xs font-medium text-gray-400 transition-colors hover:text-gray-900">
                                                    <svg class="h-3 w-3 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                    Input
                                                </button>
                                                <pre x-show="open" x-transition class="mt-1.5 max-h-40 overflow-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-600">{{ json_encode($log->input_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif

                                        {{-- Output --}}
                                        @if ($log->raw_output)
                                            <div class="mt-2" x-data="{ open: false }">
                                                <button @click="open = !open" class="flex items-center gap-1.5 text-xs font-medium text-gray-400 transition-colors hover:text-gray-900">
                                                    <svg class="h-3 w-3 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                    Output
                                                </button>
                                                <pre x-show="open" x-transition class="mt-1.5 max-h-40 overflow-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($log->raw_output, 2000) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="border-t border-gray-100 px-6 py-3">
                        <button wire:click="closeIoModal" class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-900">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
