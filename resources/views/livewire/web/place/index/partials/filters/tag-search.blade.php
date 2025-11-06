{{-- Tag search field with autocomplete (common desktop + mobile) --}}
<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <div class="bg-white border border-gray-300 rounded-lg focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-all">
        <div class="flex items-center">
            <div class="p-3 pr-2 text-gray-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.300ms="tagSearchQuery"
                   @focus="open = true"
                   placeholder="{{ __('web/pages/explore.livewire.tags_search_placeholder') }}"
                   class="flex-1 py-2 pl-0 px-2 text-sm text-gray-900 placeholder-gray-500 bg-transparent border-0 focus:ring-0 focus:outline-none">
            @if($tagSearchQuery)
                <button wire:click="$set('tagSearchQuery', '')" class="p-3 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Tag suggestions dropdown --}}
    @if(count($filteredTags) > 0)
        <div x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto">
            @foreach($filteredTags as $tag)
                <button type="button"
                        wire:click="selectTag('{{ $tag['slug'] }}')"
                        @click="open = false"
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 text-gray-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">🚀 {{ $tag['name'] }}</div>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    @endif
</div>
