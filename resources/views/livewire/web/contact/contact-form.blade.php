<div>
    <form id="contactForm"
          x-data="contactForm"
          @submit.prevent="handleSubmit"
          class="space-y-6">

        {{-- Success message --}}
        @if ($success)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start space-x-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">
                        {{ __('web/pages/contact.form.success.title') }}
                    </p>
                    <p class="text-sm text-green-700 mt-1">
                        {{ __('web/pages/contact.form.success.message') }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Error message --}}
        @if ($errorMessage)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start space-x-3">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-red-800">
                        {{ __('web/pages/contact.form.errors.title') }}
                    </p>
                    <p class="text-sm text-red-700 mt-1">
                        {{ $errorMessage }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Name field --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('web/pages/contact.form.fields.name.label') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                wire:model.blur="name"
                placeholder="{{ __('web/pages/contact.form.fields.name.placeholder') }}"
                class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email field --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('web/pages/contact.form.fields.email.label') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="email"
                id="email"
                wire:model.blur="email"
                placeholder="{{ __('web/pages/contact.form.fields.email.placeholder') }}"
                class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror"
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Subject field (optional) --}}
        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('web/pages/contact.form.fields.subject.label') }}
                <span class="text-gray-500 text-xs">{{ __('web/pages/contact.form.fields.subject.optional') }}</span>
            </label>
            <input
                type="text"
                id="subject"
                wire:model.blur="subject"
                placeholder="{{ __('web/pages/contact.form.fields.subject.placeholder') }}"
                class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('subject') border-red-500 @enderror"
            >
            @error('subject')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Message field --}}
        <div>
            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('web/pages/contact.form.fields.message.label') }} <span class="text-red-500">*</span>
            </label>
            <textarea
                id="message"
                wire:model.blur="message"
                rows="6"
                placeholder="{{ __('web/pages/contact.form.fields.message.placeholder') }}"
                class="w-full bg-white px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none @error('message') border-red-500 @enderror"
            ></textarea>
            @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit button --}}
        <div>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg text-base font-semibold hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-600/25"
            >
                <span wire:loading.remove wire:target="submit">
                    {{ __('web/pages/contact.form.submit') }}
                </span>
                <span wire:loading.flex wire:target="submit" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('web/pages/contact.form.sending') }}
                </span>
            </button>
        </div>

    </form>
</div>
