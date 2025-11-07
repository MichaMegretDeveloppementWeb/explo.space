{{-- Featured places filter toggle --}}
<div class="pt-4 border-t border-gray-200">
    <label class="flex items-start cursor-pointer group">
        <input type="checkbox"
               wire:model.live="showFeaturedOnly"
               class="h-4 w-4 mt-0.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 focus:ring-offset-1 transition-all duration-200 cursor-pointer">
        <div class="ml-3 flex-1">
            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors duration-200">
                {{ __('web/pages/explore.livewire.featured_toggle_label') }}
            </span>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ __('web/pages/explore.livewire.featured_toggle_help') }}
            </p>
        </div>
    </label>
</div>
