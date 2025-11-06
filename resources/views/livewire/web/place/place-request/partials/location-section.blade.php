{{-- Location Section --}}
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
        {{ __('web/pages/place-request.location.title') }}
    </h2>

    <div class="space-y-6">
        {{-- Address Search avec Alpine.js --}}
        <div x-data="{ open: @entangle('showSuggestions') }" class="relative">
            <label for="queryAddress" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('web/pages/place-request.location.search') }}
            </label>
            <div class="relative">
                <input type="search"
                       id="queryAddress"
                       wire:model.live.debounce.500ms="queryAddress"
                       @focus="open = true"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="{{ __('web/pages/place-request.location.search_placeholder') }}"
                       autocomplete="off">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('web/pages/place-request.location.search_help') }}
            </p>
            @error('queryAddress')
                <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            {{-- Dropdown suggestions --}}
            <div x-show="open"
                 @click.away="open = false"
                 x-transition
                 class="absolute z-[4] w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">

                @if (count($suggestions) > 0)
                    @foreach ($suggestions as $index => $suggestion)
                        <div wire:click="selectAddress({{ $index }})"
                             class="px-4 py-3 cursor-pointer hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100 last:border-b-0">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $suggestion['display_name'] }}
                            </div>
                            @if (isset($suggestion['type']))
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ ucfirst($suggestion['type']) }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                @elseif (strlen($queryAddress) >= 3)
                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                        <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        {{ __('web/pages/place-request.location.no_results') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Validated Address --}}
        @if ($placeAddress)
            <div class="rounded-lg bg-green-50 border border-green-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ __('web/pages/place-request.location.address_validated') }}</p>
                        <p class="mt-1 text-sm text-green-700">{{ $placeAddress }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Erreur reverse geocoding --}}
        @error('address')
            <div class="error-message rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-red-800">{{ $message }}</p>
                </div>
            </div>
        @enderror

        {{-- Interactive Leaflet Map (wire:ignore) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('web/pages/place-request.location.interactive_map') }}
            </label>
            <div wire:ignore
                 id="placeRequestMap"
                 class="h-96 w-full rounded-lg border-2 border-gray-300 shadow-sm overflow-hidden"
                 style="min-height: 400px;z-index: 2">
            </div>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('web/pages/place-request.location.map_help') }}
            </p>
        </div>

        {{-- Manual Coordinates --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('web/pages/place-request.location.latitude') }}
                </label>
                <input type="number"
                       step="0.000001"
                       id="latitude"
                       wire:model.live.debounce.1000ms="latitude"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="48.856614">
                @error('latitude')
                    <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('web/pages/place-request.location.longitude') }}
                </label>
                <input type="number"
                       step="0.000001"
                       id="longitude"
                       wire:model.live.debounce.1000ms="longitude"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="2.352222">
                @error('longitude')
                    <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Address Text (fallback) --}}
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('web/pages/place-request.location.address_field') }}
            </label>
            <input type="text"
                   id="address"
                   wire:model.live.debounce.500ms="address"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="{{ __('web/pages/place-request.location.address_placeholder') }}">
            <p class="mt-1 text-sm text-gray-500">
                {{ __('web/pages/place-request.location.address_help') }}
            </p>
            @error('address')
                <p class="error-message mt-1 text-sm text-red-600">{{ $message }} - {{ __('web/pages/place-request.location.manual_entry_hint') }}</p>
            @enderror
        </div>
    </div>
</div>
