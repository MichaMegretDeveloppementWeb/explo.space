{{-- Common address search form (mobile + desktop) --}}
<div class="relative" x-data="{ open: false }" @click.away="open = false">

    <div class="flex items-center">

        <div class="flex items-center flex-1 bg-white rounded-lg shadow-sm border border-gray-300 focus-within:shadow-md focus-within:border-blue-500 transition-all duration-200">

            <div class="p-2 text-gray-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <input type="text"
                   wire:model.live.debounce.300ms="address"
                   @focus="open = true"
                   placeholder="{{ __('web/pages/explore.livewire.address_placeholder') }}"
                   class="flex-1 py-2 px-0 text-gray-900 placeholder-gray-500 bg-transparent border-0 focus:ring-0 focus:outline-none">

            @if($address)
                <button wire:click="$set('address', '')" class="p-2 pr-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif

            <button wire:click="requestGeolocation"
                    @disabled($geolocLoading)
                    class="p-2 pl-1 text-gray-400 hover:text-gray-600 cursor-pointer disabled:opacity-50 transition-colors rounded-md">
                @if($geolocLoading)
                    <div class="animate-spin h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                @else
                    <x-icons.crosshair class="h-5 w-5 text-blue-500" />
                @endif
            </button>

        </div>

    </div>

    {{-- Erreurs de recherche d'adresse --}}
    @error('address_search')
        <div class="mt-2 rounded-md bg-red-50 border border-red-200 p-2">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-red-400 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="ml-2 text-xs text-red-700">{{ $message }}</p>
            </div>
        </div>
    @enderror

    {{-- Erreurs de géolocalisation --}}
    @error('geolocation')
        <div class="mt-2 rounded-md bg-red-50 border border-red-200 p-2">
            <div class="flex items-start">
                <svg class="h-4 w-4 text-red-400 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="ml-2 text-xs text-red-700">{{ $message }}</p>
            </div>
        </div>
    @enderror

    {{-- Suggestions dropdown --}}
    @if(count($addressSuggestions) > 0)
        <div x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto">
            @foreach($addressSuggestions as $index => $suggestion)
                <button type="button"
                        wire:click="selectAddressSuggestion({{ $index }})"
                        @click="open = false"
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-5 h-5 text-gray-400">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $suggestion['display_name'] }}</div>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    @endif
</div>
