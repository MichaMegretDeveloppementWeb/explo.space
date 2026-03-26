@php
    $items = $message->payload['items'] ?? [];
    $isCheckpointActive = $this->activeWorkflow && $this->activeWorkflow->status === \App\Enums\AutofillWorkflowStatus::AwaitingSelection;
@endphp

<div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5">
        <h4 class="mb-4 text-[15px] font-semibold text-gray-900">
            {{ $message->payload['text'] ?? 'Sélectionnez les lieux à enrichir' }}
        </h4>

        @if ($isCheckpointActive && ! empty($items))
            <div x-data="{
                selected: @entangle('selectedItems'),
                allIds: @js(array_column($items, 'id')),
                get allSelected() { return this.allIds.length > 0 && this.allIds.every(id => this.selected.includes(id)); },
                toggleAll() {
                    if (this.allSelected) {
                        this.selected = [];
                    } else {
                        this.selected = [...this.allIds];
                    }
                },
                toggle(id) {
                    const idx = this.selected.indexOf(id);
                    if (idx > -1) {
                        this.selected.splice(idx, 1);
                    } else {
                        this.selected.push(id);
                    }
                }
            }">
                <div class="mb-3 flex items-center justify-between">
                    <button @click="toggleAll()" type="button" class="cursor-pointer text-xs font-medium text-gray-500 transition-colors hover:text-gray-900">
                        <span x-text="allSelected ? 'Tout désélectionner' : 'Tout sélectionner'"></span>
                    </button>
                    <span class="text-xs text-gray-400" x-text="selected.length + '/' + allIds.length + ' sélectionné(s)'"></span>
                </div>

                <div class="space-y-2">
                    @foreach ($items as $item)
                        <label wire:key="select-item-{{ $item['id'] }}" @click="toggle({{ $item['id'] }})"
                               class="flex cursor-pointer items-start gap-3 rounded-xl border p-3.5 transition-all"
                               :class="selected.includes({{ $item['id'] }}) ? 'border-gray-900 bg-gray-50' : 'border-gray-100 hover:border-gray-200'">
                            <div class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded border transition-colors"
                                 :class="selected.includes({{ $item['id'] }}) ? 'border-gray-900 bg-gray-900' : 'border-gray-300'">
                                <svg x-show="selected.includes({{ $item['id'] }})" class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item['name'] ?? '' }}</p>
                                @if (! empty($item['location']))
                                    <p class="mt-0.5 text-xs text-gray-400">{{ $item['location'] }}</p>
                                @endif
                                @if (! empty($item['justification']))
                                    <p class="mt-1 text-xs text-gray-400">{{ $item['justification'] }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="mt-5 flex justify-end">
                    <button wire:click="submitSelection"
                            class="cursor-pointer rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800">
                        Valider la sélection
                    </button>
                </div>
            </div>
        @else
            @php
                $selectedIds = $message->payload['selected_ids'] ?? [];
            @endphp
            <div class="space-y-2">
                @foreach ($items as $item)
                    @php
                        $isSelected = in_array($item['id'], $selectedIds);
                    @endphp
                    <div wire:key="select-item-{{ $item['id'] }}"
                         class="rounded-xl px-4 py-3 {{ $isSelected ? 'border border-gray-900 bg-gray-50' : 'bg-gray-50/50 opacity-50' }}">
                        <p class="text-sm {{ $isSelected ? 'text-gray-900 font-medium' : 'text-gray-400 line-through' }}">{{ $item['name'] ?? '' }}</p>
                        @if (! empty($item['location']))
                            <p class="mt-0.5 text-xs text-gray-400">{{ $item['location'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
