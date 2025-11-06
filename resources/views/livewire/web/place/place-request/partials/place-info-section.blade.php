{{-- Place Information Section --}}
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
        {{ __('web/pages/place-request.place_info.title') }}
    </h2>

    <div class="space-y-4">
        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('web/pages/place-request.place_info.name') }}
                <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="title"
                   wire:model="title"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="{{ __('web/pages/place-request.place_info.name_placeholder') }}">
            @error('title')
                <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('web/pages/place-request.place_info.description') }}
            </label>
            <textarea id="description"
                      wire:model="description"
                      rows="6"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="{{ __('web/pages/place-request.place_info.description_placeholder') }}"></textarea>
            @error('description')
                <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Practical Info --}}
        <div>
            <label for="practical_info" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('web/pages/place-request.place_info.practical_info') }}
            </label>
            <textarea id="practical_info"
                      wire:model="practical_info"
                      rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      placeholder="{{ __('web/pages/place-request.place_info.practical_info_placeholder') }}"></textarea>
            @error('practical_info')
                <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500">
                {{ __('web/pages/place-request.place_info.practical_info_help') }}
            </p>
        </div>
    </div>
</div>
