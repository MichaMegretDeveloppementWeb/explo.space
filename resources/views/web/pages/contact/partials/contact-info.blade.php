<!-- Section Informations de contact -->
<section class="bg-gray-50 py-12 sm:py-16 md:py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                {{ __('web/pages/contact.contact_info.title') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <!-- Email -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 mb-4 mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-2">
                        {{ __('web/pages/contact.contact_info.email.label') }}
                    </p>
                    <a href="mailto:{{ __('web/pages/contact.contact_info.email.value') }}" class="text-base font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                        {{ __('web/pages/contact.contact_info.email.value') }}
                    </a>
                </div>

                <!-- Response time -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-4 mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-2">
                        {{ __('web/pages/contact.contact_info.response_time.label') }}
                    </p>
                    <p class="text-base font-semibold text-gray-900">
                        {{ __('web/pages/contact.contact_info.response_time.value') }}
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
