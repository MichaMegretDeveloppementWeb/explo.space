{{-- Contact Section --}}
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
        {{ __('web/pages/place-request.contact.title') }}
    </h2>

    <div>
        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
            {{ __('web/pages/place-request.contact.email') }}
            <span class="text-red-500">*</span>
        </label>
        <input type="email"
               id="contact_email"
               wire:model="contact_email"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="{{ __('web/pages/place-request.contact.email_placeholder') }}">
        @error('contact_email')
            <p class="error-message mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-gray-500">
            {{ __('web/pages/place-request.contact.email_help') }}
        </p>
    </div>
</div>
