{{-- Selected tags chips (common desktop + mobile) --}}
@if(count($selectedTags) > 0)
    <div class="flex flex-wrap gap-2">
        @foreach($selectedTags as $tag)
            <div class="flex items-center bg-blue-100 border border-blue-200 rounded-full px-3 py-1">
                <span class="text-xs text-blue-800 font-medium">🚀 {{ $tag['name'] }}</span>
                <button wire:click="removeTag('{{ $tag['slug'] }}')" class="ml-2 text-blue-600 hover:text-blue-800">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
