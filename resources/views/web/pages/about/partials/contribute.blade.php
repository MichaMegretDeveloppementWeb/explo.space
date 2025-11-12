<!-- Section Contribuer -->
<section id="contribute" class="bg-gray-50 py-12 sm:py-16 md:py-20 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Titre et sous-titre -->
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                {{ __('web/pages/about.contribute.title') }}
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                {{ __('web/pages/about.contribute.subtitle') }}
            </p>
        </div>

        <!-- Pourquoi contribuer -->
        <div class="mb-12 sm:mb-16">
            <h3 class="text-2xl sm:text-3xl font-semibold text-gray-900 text-center mb-8">
                {{ __('web/pages/about.contribute.why_contribute.title') }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <!-- Raison 1 -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 mb-4 mx-auto">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">
                        {{ __('web/pages/about.contribute.why_contribute.reasons.share_passion.title') }}
                    </h4>
                    <p class="text-gray-600 text-center">
                        {{ __('web/pages/about.contribute.why_contribute.reasons.share_passion.description') }}
                    </p>
                </div>

                <!-- Raison 2 -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600 mb-4 mx-auto">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">
                        {{ __('web/pages/about.contribute.why_contribute.reasons.enrich_database.title') }}
                    </h4>
                    <p class="text-gray-600 text-center">
                        {{ __('web/pages/about.contribute.why_contribute.reasons.enrich_database.description') }}
                    </p>
                </div>

                <!-- Raison 3 -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-4 mx-auto">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">
                        {{ __('web/pages/about.contribute.why_contribute.reasons.quality.title') }}
                    </h4>
                    <p class="text-gray-600 text-center">
                        {{ __('web/pages/about.contribute.why_contribute.reasons.quality.description') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Comment contribuer -->
        <div class="max-w-4xl mx-auto">
            <h3 class="text-2xl sm:text-3xl font-semibold text-gray-900 text-center mb-8">
                {{ __('web/pages/about.contribute.how_to_contribute.title') }}
            </h3>

            <div class="space-y-8">
                <!-- Étape 1 -->
                <div class="flex flex-col sm:flex-row items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">
                        1
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ __('web/pages/about.contribute.how_to_contribute.steps.propose.title') }}
                        </h4>
                        <p class="text-gray-600 leading-relaxed">
                            {{ __('web/pages/about.contribute.how_to_contribute.steps.propose.description') }}
                        </p>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="flex flex-col sm:flex-row items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-purple-600 text-white font-bold">
                        2
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ __('web/pages/about.contribute.how_to_contribute.steps.validation.title') }}
                        </h4>
                        <p class="text-gray-600 leading-relaxed">
                            {{ __('web/pages/about.contribute.how_to_contribute.steps.validation.description') }}
                        </p>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="flex flex-col sm:flex-row items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-green-600 text-white font-bold">
                        3
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ __('web/pages/about.contribute.how_to_contribute.steps.publication.title') }}
                        </h4>
                        <p class="text-gray-600 leading-relaxed">
                            {{ __('web/pages/about.contribute.how_to_contribute.steps.publication.description') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-12">
                <a href="{{ localRoute('place_requests.create') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/25">
                    {{ __('web/pages/about.contribute.cta') }}
                </a>
            </div>
        </div>

    </div>
</section>
