{{-- Mode selector buttons (common mobile + desktop) --}}
<div class="mt-4 flex bg-gray-100 rounded-lg p-1">
    <button wire:click="switchToProximity"
        @class([
            'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200',
            'bg-blue-600 text-white shadow-md' => $searchMode === 'proximity',
            'text-gray-600 hover:text-gray-900 cursor-pointer' => $searchMode !== 'proximity'
        ])>
        {{ __('web/pages/explore.livewire.mode_proximity') }}
    </button>
    <button wire:click="switchToWorldwide"
        @class([
            'flex-1 py-2 px-4 rounded-md text-sm font-medium transition-all duration-200',
            'bg-blue-600 text-white shadow-md' => $searchMode === 'worldwide',
            'text-gray-600 hover:text-gray-900 cursor-pointer' => $searchMode !== 'worldwide'
        ])>
        {{ __('web/pages/explore.livewire.mode_worldwide') }}
    </button>
</div>
