{{-- Photo Suggestion Form Component --}}
<div class="mt-0 max-w-7xl mx-auto ">

    <x-web.flash-messages/>

    {{-- Button to open form --}}
    @if(!$showForm)
        <div class="flex justify-center">
            <button type="button"
                    wire:click="toggleForm"
                    class="w-max sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-white text-gray-900 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ __('web/pages/place-show.photo_suggestion.button') }}
            </button>
        </div>
    @endif

    {{-- Form (visible when showForm = true) --}}
    @if($showForm)
        <div class="bg-white rounded-lg shadow-md p-6"
             x-data="{
                 async submitForm() {
                     try {
                         const token = await window.recaptcha.getToken('photo_suggestion_submit');
                         @this.call('submit', token);
                     } catch (error) {
                         @this.call('handleRecaptchaError', error.message);
                     }
                 }
             }">

            {{-- Header --}}
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ __('web/pages/place-show.photo_suggestion.title') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('web/pages/place-show.photo_suggestion.subtitle') }}
                    </p>
                </div>
                <button type="button"
                        wire:click="toggleForm"
                        class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Upload Area --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('web/pages/place-show.photo_suggestion.photos_label') }}
                </label>

                <div id="photo-drop-zone" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors cursor-pointer">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center items-center">
                            <label for="photos"
                                   class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500"
                                   @click.stop>
                                <span>{{ __('web/pages/place-show.photo_suggestion.upload_button') }}</span>
                                <input id="photos"
                                       type="file"
                                       wire:model.live="pendingPhotos"
                                       multiple
                                       accept="image/jpeg,image/jpg,image/png,image/webp"
                                       class="sr-only">
                            </label>
                            <p class="pl-1">{{ __('web/pages/place-show.photo_suggestion.drag_drop') }}</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ __('web/pages/place-show.photo_suggestion.file_requirements') }}
                        </p>
                    </div>
                </div>

                {{-- Validation Errors --}}
                @error('pendingPhotos')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('pendingPhotos.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Loading Indicator --}}
                <div wire:loading wire:target="pendingPhotos" class="mt-3 rounded-md bg-blue-50 border border-blue-200 p-3">
                    <div class="flex items-center">
                        <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-blue-800">{{ __('web/pages/place-show.photo_suggestion.validating') }}</span>
                    </div>
                </div>
            </div>

            {{-- Photo Previews --}}
            @if(count($photos) > 0)
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">
                        {{ __('web/pages/place-show.photo_suggestion.selected_photos') }} ({{ count($photos) }})
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($photos as $index => $photo)
                            <div class="relative group" wire:key="photo-{{ $index }}">
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 border-gray-300">
                                    @if($photo)
                                        <img src="{{ $photo->temporaryUrl() }}"
                                             alt="Photo {{ $index + 1 }}"
                                             class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <button type="button"
                                        wire:click="removePhoto({{ $index }})"
                                        class="absolute top-2 right-2 p-1.5 bg-white/95 backdrop-blur-sm rounded-md shadow-sm hover:bg-red-50 transition-all group/btn">
                                    <svg class="w-4 h-4 text-gray-600 group-hover/btn:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Contact Email --}}
            <div class="mb-6">
                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('web/pages/place-show.photo_suggestion.contact_email') }}
                </label>
                <input type="email"
                       id="contact_email"
                       wire:model="contact_email"
                       placeholder="{{ __('web/pages/place-show.photo_suggestion.email_placeholder') }}"
                       class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('contact_email') border-red-300 @enderror">
                @error('contact_email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Error --}}
            @error('submit')
                <div class="mb-6 rounded-md bg-red-50 border border-red-200 p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            @enderror

            {{-- Actions --}}
            <div class="flex justify-end space-x-3">
                <button type="button"
                        wire:click="toggleForm"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('web/pages/place-show.photo_suggestion.cancel') }}
                </button>
                <button type="button"
                        @click="submitForm()"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="submit">
                        {{ __('web/pages/place-show.photo_suggestion.submit') }}
                    </span>
                    <span wire:loading.flex wire:target="submit" class="flex items-center">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('web/pages/place-show.photo_suggestion.submitting') }}
                    </span>
                </button>
            </div>
        </div>
    @endif
</div>
