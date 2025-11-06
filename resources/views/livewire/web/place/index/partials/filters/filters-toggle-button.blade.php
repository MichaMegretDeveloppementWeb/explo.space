{{-- Bouton toggle Filtres (toujours visible) --}}
<div class="flex-shrink-0 border-b border-gray-200">
    <button @click="toggleFilters()"
            class="w-full px-4 py-3 flex items-center justify-between text-gray-700 hover:bg-gray-50 transition-colors group cursor-pointer">

        {{-- Left side: Label + icône --}}
        <div class="flex items-center space-x-2">
            <x-heroicon-o-funnel class="w-5 h-5" />
            <span class="text-md font-normal">{{ __('web/pages/explore.livewire.filters_tags_label') }}</span>
        </div>

        {{-- Right side: Chevron dynamique --}}
        <div class="transition-transform duration-200"
             :class="{ 'rotate-180': !filtersCollapsed }">
            <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" />
        </div>
    </button>
</div>
