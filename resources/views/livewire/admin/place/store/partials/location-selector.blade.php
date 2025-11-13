<div class="space-y-6">
    {{-- Address Search avec Alpine.js --}}
    <div x-data="{ open: @entangle('showSuggestions') }" class="relative" @click.away="open = false">
        <x-admin.form.input
            type="search"
            label="Rechercher une adresse"
            name="queryAddress"
            wire:model.live.debounce.500ms="queryAddress"
            placeholder="Tapez une adresse pour rechercher..."
            helperText="Saisissez au moins 3 caractères pour voir les suggestions"
            :error="$errors->first('queryAddress')"
            @focus="open = true"
        >
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </x-admin.form.input>

        {{-- Dropdown suggestions --}}
        <div x-show="open"
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
                    Aucune adresse trouvée
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
                    <p class="text-sm font-medium text-green-800">Adresse validée</p>
                    <p class="mt-1 text-sm text-green-700">{{ $placeAddress }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Erreur reverse geocoding --}}
    @error('placeAddress')
        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="error-message text-sm text-red-800">{{ $message }}</p>
            </div>
        </div>
    @enderror

    {{-- Interactive Leaflet Map (wire:ignore) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Carte interactive
        </label>

        <div class="relative">
            {{-- Loader (EN DEHORS du wire:ignore) --}}
            <x-admin.map-loader />

            {{-- Carte (DANS le wire:ignore) --}}
            <div wire:ignore>
                <div id="admin-location-map"
                     data-latitude="{{ $latitude }}"
                     data-longitude="{{ $longitude }}"
                     data-original-latitude="{{ $originalLatitude }}"
                     data-original-longitude="{{ $originalLongitude }}"
                     class="h-96 w-full rounded-lg border-2 border-gray-300 shadow-sm overflow-hidden"
                     style="min-height: 400px;z-index: 2">
                </div>
            </div>
        </div>

        <p class="mt-2 text-sm text-gray-500">
            Cliquez sur la carte pour définir les coordonnées, ou déplacez le marqueur
        </p>
    </div>

    {{-- Manual Coordinates --}}
    @if($originalLatitude && $originalLongitude)
        <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-xs font-medium text-amber-800">Anciennes coordonnées :</p>
                    <p class="text-sm text-amber-700 mt-1">
                        Latitude: {{ number_format($originalLatitude, 6) }} / Longitude: {{ number_format($originalLongitude, 6) }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-admin.form.input
            label="Latitude"
            name="latitude"
            id="latitude"
            type="number"
            wire:model.live.debounce.1000ms="latitude"
            step="0.000001"
            :required="true"
            :error="$errors->first('latitude')"
        />

        <x-admin.form.input
            label="Longitude"
            name="longitude"
            id="longitude"
            type="number"
            wire:model.live.debounce.1000ms="longitude"
            step="0.000001"
            :required="true"
            :error="$errors->first('longitude')"
        />
    </div>

    {{-- Address Text (fallback) --}}
    <x-admin.form.input
        label="Adresse réelle du lieu"
        name="address"
        wire:model.live.debounce.500ms="address"
        placeholder="Adresse complète"
        helperText="Adresse qui sera enregistrée et visible par les utilisateurs"
        :required="true"
        :error="$errors->first('address')"
    />
</div>
