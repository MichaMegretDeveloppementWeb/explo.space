<div>

    <x-web.flash-messages/>

    {{-- Boutons d'ouverture du formulaire --}}
    @if (!$showForm)
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button wire:click="openSignalementForm"
                    type="button"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-800 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ __('web/pages/place-show.actions.report_error') }}</span>
            </button>

            <button wire:click="openModificationForm"
                    type="button"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-gray-900 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>{{ __('web/pages/place-show.actions.suggest_edit') }}</span>
            </button>
        </div>
    @endif

    {{-- Formulaire --}}
    @if ($showForm)
        <div class="mt-6 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                @if ($type === 'signalement')
                    {{ __('web/pages/place-show.actions.report_error') }}
                @else
                    {{ __('web/pages/place-show.actions.suggest_edit') }}
                @endif
            </h3>

            <form x-data="editRequestForm" @submit.prevent="handleSubmit">
                {{-- Champs à modifier (si modification) --}}
                @if ($type === 'modification')
                    <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            {{ __('web/pages/place-show.edit_request.select_fields') }}
                        </label>

                        <div class="space-y-3">
                            {{-- Titre --}}
                            <div class="flex flex-col items-start">

                                <div class="flex items-center gap-3 w-full">

                                    <input type="checkbox"
                                           id="field_title"
                                           wire:model.live="selected_fields"
                                           value="title"
                                           class="form-checkbox min-h-4 min-w-4 text-blue-600">

                                    <label for="field_title" class="text-sm font-medium text-gray-900 cursor-pointer block w-full py-2">
                                        {{ __('web/pages/place-show.edit_request.field_title') }}
                                    </label>

                                </div>

                                <div class="w-full flex-1">
                                    @if (in_array('title', $selected_fields))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 mb-1">
                                                {{ __('web/pages/place-show.edit_request.current_value') }} :
                                            </p>
                                            <p class="text-sm text-gray-700 line-through">{{ $current_values['title'] }}</p>

                                            <input type="text"
                                                   wire:model="new_values.title"
                                                   class="mt-2 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                                                   placeholder="{{ __('web/pages/place-show.edit_request.new_value_placeholder') }}">
                                            @error('new_values.title')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- Description --}}
                            <div class="flex flex-col items-start">

                                <div class="flex items-center gap-3 w-full">

                                    <input type="checkbox"
                                           id="field_description"
                                           wire:model.live="selected_fields"
                                           value="description"
                                           class="form-checkbox min-h-4 min-w-4 text-blue-600">

                                    <label for="field_description" class="text-sm font-medium text-gray-900 cursor-pointer block w-full py-2">
                                        {{ __('web/pages/place-show.edit_request.field_description') }}
                                    </label>

                                </div>

                                <div class="w-full flex-1">
                                    @if (in_array('description', $selected_fields))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 mb-1">
                                                {{ __('web/pages/place-show.edit_request.current_value') }} :
                                            </p>
                                            <p class="text-sm text-gray-700 line-through">{{ Str::limit($current_values['description'], 100) }}</p>

                                            <textarea wire:model="new_values.description"
                                                      rows="4"
                                                      class="mt-2 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                                                      placeholder="{{ __('web/pages/place-show.edit_request.new_value_placeholder') }}"></textarea>
                                            @error('new_values.description')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- Coordonnées GPS --}}
                            <div class="flex flex-col items-start">

                                <div class="flex items-center gap-3 w-full">

                                    <input type="checkbox"
                                           id="field_coordinates"
                                           wire:model.live="selected_fields"
                                           value="coordinates"
                                           class="form-checkbox min-h-4 min-w-4 text-blue-600">

                                    <label for="field_coordinates" class="text-sm font-medium text-gray-900 cursor-pointer block w-full py-2">
                                        {{ __('web/pages/place-show.edit_request.field_coordinates') }}
                                    </label>

                                </div>

                                <div class="w-full flex-1">
                                    @if (in_array('coordinates', $selected_fields))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 mb-1">
                                                {{ __('web/pages/place-show.edit_request.current_value') }} :
                                            </p>
                                            <p class="text-sm text-gray-700 line-through">
                                                {{ $current_values['coordinates']['lat'] }}, {{ $current_values['coordinates']['lng'] }}
                                            </p>

                                            <div class="mt-2 grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Latitude</label>
                                                    <input type="number"
                                                           wire:model.live.debounce.500ms="new_values.coordinates.lat"
                                                           step="0.000001"
                                                           class="block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                                                    @error('new_values.coordinates.lat')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Longitude</label>
                                                    <input type="number"
                                                           wire:model.live.debounce.500ms="new_values.coordinates.lng"
                                                           step="0.000001"
                                                           class="block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                                                    @error('new_values.coordinates.lng')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Carte Leaflet --}}
                                            <div class="mt-3">
                                                {{-- Élément de données pour synchronisation (mis à jour par Livewire) --}}
                                                <div id="edit-request-map-data"
                                                     class="hidden"
                                                     data-lat="{{ $new_values['coordinates']['lat'] ?? $current_values['coordinates']['lat'] }}"
                                                     data-lng="{{ $new_values['coordinates']['lng'] ?? $current_values['coordinates']['lng'] }}">
                                                </div>

                                                <div wire:ignore class="relative" wire:key="map-section">
                                                    {{-- Loader (EN DEHORS du wire:ignore) --}}
                                                    <x-web.map-loader />

                                                    {{-- Wrapper avec wire:ignore pour protéger Leaflet --}}
                                                    <div>
                                                        {{-- Conteneur de la carte --}}
                                                        <div id="edit-request-map"
                                                             class="h-64 rounded-lg border border-gray-300">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- Adresse --}}
                            <div class="flex flex-col items-start">

                                <div class="flex items-center gap-3 w-full">

                                    <input type="checkbox"
                                           id="field_address"
                                           wire:model.live="selected_fields"
                                           value="address"
                                           class="form-checkbox min-h-4 min-w-4 text-blue-600">

                                    <label for="field_address" class="text-sm font-medium text-gray-900 cursor-pointer block w-full py-2">
                                        {{ __('web/pages/place-show.edit_request.field_address') }}
                                    </label>

                                </div>

                                <div class="w-full flex-1">
                                    @if (in_array('address', $selected_fields))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 mb-1">
                                                {{ __('web/pages/place-show.edit_request.current_value') }} :
                                            </p>
                                            <p class="text-sm text-gray-700 line-through">{{ $current_values['address'] }}</p>

                                            <input type="text"
                                                   wire:model="new_values.address"
                                                   class="mt-2 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                                                   placeholder="{{ __('web/pages/place-show.edit_request.new_value_placeholder') }}">
                                            @error('new_values.address')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- Informations pratiques --}}
                            <div class="flex flex-col items-start">

                                <div class="flex items-center gap-3 w-full">

                                    <input type="checkbox"
                                           id="field_practical_info"
                                           wire:model.live="selected_fields"
                                           value="practical_info"
                                           class="form-checkbox min-h-4 min-w-4 text-blue-600">

                                    <label for="field_practical_info" class="text-sm font-medium text-gray-900 cursor-pointer block w-full py-2">
                                        {{ __('web/pages/place-show.edit_request.field_practical_info') }}
                                    </label>

                                </div>

                                <div class="w-full flex-1">
                                    @if (in_array('practical_info', $selected_fields))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-600 mb-1">
                                                {{ __('web/pages/place-show.edit_request.current_value') }} :
                                            </p>
                                            <p class="text-sm text-gray-700 line-through">{{ Str::limit($current_values['practical_info'], 100) }}</p>

                                            <textarea wire:model="new_values.practical_info"
                                                      rows="4"
                                                      class="mt-2 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                                                      placeholder="{{ __('web/pages/place-show.edit_request.new_value_placeholder') }}"></textarea>
                                            @error('new_values.practical_info')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                            </div>

                        </div>

                        @error('selected_fields')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Description / Commentaire --}}
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        @if ($type === 'signalement')
                            {{ __('web/pages/place-show.edit_request.description_label_signalement') }}
                        @else
                            {{ __('web/pages/place-show.edit_request.description_label_modification') }}
                        @endif
                    </label>

                    <textarea wire:model="description"
                              id="description"
                              rows="4"
                              class="block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                              placeholder="@if ($type === 'signalement'){{ __('web/pages/place-show.edit_request.description_placeholder_signalement') }}@else{{ __('web/pages/place-show.edit_request.description_placeholder_modification') }}@endif"></textarea>

                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email de contact --}}
                <div class="mb-6">
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('web/pages/place-show.edit_request.contact_email_label') }}
                    </label>

                    <input type="email"
                           wire:model="contact_email"
                           id="contact_email"
                           class="block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"
                           placeholder="{{ __('web/pages/place-show.edit_request.contact_email_placeholder') }}">

                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- reCAPTCHA v3 --}}
                <input type="hidden" wire:model="recaptcha_token" id="recaptcha_token">

                @error('recaptcha_token')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('submit')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('type')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Boutons d'action --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        {{-- Spinner (affiché pendant le chargement) --}}
                        <svg wire:loading wire:target="submit" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>

                        {{-- Texte normal (caché pendant le chargement) --}}
                        <span wire:loading.remove wire:target="submit">
                            {{ __('web/pages/place-show.edit_request.submit') }}
                        </span>

                        {{-- Texte de chargement (affiché pendant le chargement) --}}
                        <span wire:loading wire:target="submit">
                            {{ __('web/pages/place-show.edit_request.submitting') }}
                        </span>
                    </button>

                    <button type="button"
                            wire:click="toggleForm"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('web/pages/place-show.edit_request.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
