{{-- Form Actions --}}
<div class="space-y-4">

    {{-- reCAPTCHA Hidden Field --}}
    <input type="hidden" wire:model="recaptcha_token" id="recaptcha_token">

    {{-- Errors Display --}}
    @error('recaptcha_token')
    <div class="error-message px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        {{ $message }}
    </div>
    @enderror

    @error('submit')
    <div class="error-message px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        {{ $message }}
    </div>
    @enderror

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
        <a href="{{ route('home.'.app()->getLocale()) }}"
           class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 text-center">
            {{ __('web/pages/place-request.actions.cancel') }}
        </a>

        <button type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
            <span wire:loading.remove wire:target="submit">
                {{ __('web/pages/place-request.actions.submit') }}
            </span>
            <span wire:loading wire:target="submit">
                {{ __('web/pages/place-request.actions.submitting') }}
            </span>
        </button>
    </div>

</div>
