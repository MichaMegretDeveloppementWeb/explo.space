@php
    $itemsWithImages = $message->payload['items'] ?? [];
    $isCheckpointActive = $this->activeWorkflow && $this->activeWorkflow->status === \App\Enums\AutofillWorkflowStatus::AwaitingImages;
@endphp

<div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5">
        <h4 class="mb-4 text-[15px] font-semibold text-gray-900">
            {{ $message->payload['text'] ?? 'Sélectionnez les images à conserver' }}
        </h4>

        @if ($isCheckpointActive && ! empty($itemsWithImages))
            <div x-data="{
                selected: Object.assign({}, @entangle('selectedImages')),
                syncBack() {
                    $wire.selectedImages = JSON.parse(JSON.stringify(this.selected));
                },
                toggleImage(itemId, imageIdx) {
                    if (!this.selected[itemId]) {
                        this.selected[itemId] = [];
                    }
                    const idx = this.selected[itemId].indexOf(imageIdx);
                    if (idx > -1) {
                        this.selected[itemId].splice(idx, 1);
                    } else {
                        this.selected[itemId].push(imageIdx);
                    }
                    this.syncBack();
                },
                isSelected(itemId, imageIdx) {
                    return this.selected[itemId] && this.selected[itemId].includes(imageIdx);
                },
                selectAllForItem(itemId, count) {
                    this.selected[itemId] = Array.from({length: count}, (_, i) => i);
                    this.syncBack();
                },
                deselectAllForItem(itemId) {
                    this.selected[itemId] = [];
                    this.syncBack();
                }
            }">
                <div class="space-y-6">
                    @foreach ($itemsWithImages as $itemData)
                        @php
                            $itemId = $itemData['id'];
                            $images = $itemData['images'] ?? [];
                        @endphp

                        <div wire:key="img-item-{{ $itemData['id'] }}">
                            <div class="mb-2.5 flex items-center justify-between">
                                <h5 class="text-sm font-medium text-gray-900">{{ $itemData['name'] ?? '' }}</h5>
                                @if (! empty($images))
                                    <div class="flex gap-3">
                                        <button @click="selectAllForItem({{ $itemId }}, {{ count($images) }})" type="button" class="cursor-pointer text-xs text-gray-500 transition-colors hover:text-gray-900">
                                            Tout
                                        </button>
                                        <button @click="deselectAllForItem({{ $itemId }})" type="button" class="cursor-pointer text-xs text-gray-400 transition-colors hover:text-gray-600">
                                            Aucune
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if (empty($images))
                                <p class="text-xs text-gray-400">Aucune image trouvée pour ce lieu.</p>
                            @else
                                <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                                    @foreach ($images as $idx => $image)
                                        <div wire:key="img-{{ $itemData['id'] }}-{{ $idx }}" @click="toggleImage({{ $itemId }}, {{ $idx }})"
                                             class="group relative cursor-pointer overflow-hidden rounded-xl transition-all"
                                             :class="isSelected({{ $itemId }}, {{ $idx }}) ? 'ring-2 ring-gray-900 ring-offset-1' : 'ring-1 ring-gray-100 hover:ring-gray-200'">
                                            @if (! empty($image['url']))
                                                <img src="{{ $image['url'] }}" alt="{{ $image['caption'] ?? '' }}" class="h-20 w-full object-cover" loading="lazy">
                                            @else
                                                <div class="flex h-20 w-full items-center justify-center bg-gray-50">
                                                    <svg class="h-5 w-5 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif

                                            {{-- Coche --}}
                                            <div class="absolute right-1 top-1"
                                                 x-show="isSelected({{ $itemId }}, {{ $idx }})"
                                                 x-transition>
                                                <div class="rounded-full bg-gray-900 p-0.5">
                                                    <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            {{-- Source --}}
                                            @if (! empty($image['source']))
                                                <div class="absolute bottom-0 left-0 right-0 bg-black/40 px-1.5 py-0.5">
                                                    <span class="text-[10px] text-white/80">{{ $image['source'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex justify-end">
                    <button wire:click="submitImageSelection"
                            class="cursor-pointer rounded-full bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800">
                        Valider les images
                    </button>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($itemsWithImages as $itemData)
                    @php
                        $imageCount = count($itemData['images'] ?? []);
                    @endphp
                    <div wire:key="img-item-{{ $itemData['id'] }}" class="rounded-xl bg-gray-50 px-4 py-3">
                        <p class="text-sm text-gray-600">{{ $itemData['name'] ?? '' }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">{{ $imageCount }} image(s) sélectionnée(s)</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
