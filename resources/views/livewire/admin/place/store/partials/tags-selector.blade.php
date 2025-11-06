<div>
    <label class="block text-sm font-medium text-gray-900 mb-3">
        Tags
        <span class="text-gray-500 font-normal">(thématiques publiques)</span>
    </label>

    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4">
        @forelse ($availableTags as $tag)
            <label class="flex items-center p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                <input type="checkbox"
                       wire:model.live="tagIds"
                       value="{{ $tag->id }}"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <span class="ml-3 text-sm text-gray-700">
                    {{ $tag->translations->first()->name ?? 'Sans nom' }}
                </span>
            </label>
        @empty
            <div class="text-center py-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <p class="mt-2 text-sm text-gray-500">Aucun tag disponible</p>
                <p class="mt-1 text-xs text-gray-400">Les tags doivent être créés au préalable</p>
            </div>
        @endforelse
    </div>

    @if (count($tagIds) > 0)
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($availableTags->whereIn('id', $tagIds) as $tag)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $tag->translations->first()->name ?? 'Sans nom' }}
                    <button type="button"
                            wire:click="$set('tagIds', {{ json_encode(array_values(array_diff($tagIds, [$tag->id]))) }})"
                            class="ml-2 inline-flex items-center p-0.5 text-blue-400 hover:text-blue-600">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </span>
            @endforeach
        </div>
    @endif

    @error('tagIds')
        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $message }}</span>
        </p>
    @enderror

    @error('tagIds.*')
    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ $message }}</span>
    </p>
    @enderror

    <p class="mt-1.5 text-xs text-gray-500">
        Les tags sont visibles publiquement et utilisés pour le filtrage thématique
    </p>
</div>
