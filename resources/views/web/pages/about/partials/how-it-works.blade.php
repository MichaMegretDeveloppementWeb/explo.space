<!-- Section Comment ça marche -->
<section id="how-it-works" class="bg-white py-12 sm:py-16 md:py-20 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Titre et sous-titre -->
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                {{ __('web/pages/about.how_it_works.title') }}
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                {{ __('web/pages/about.how_it_works.subtitle') }}
            </p>
        </div>

        <!-- Étapes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">

            <!-- Étape 1 : Recherche -->
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 font-bold text-lg mb-4">
                    1
                </div>
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-3">
                    {{ __('web/pages/about.how_it_works.steps.search.title') }}
                </h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('web/pages/about.how_it_works.steps.search.description') }}
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.search.features.proximity') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.search.features.worldwide') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.search.features.map') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Étape 2 : Découverte -->
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600 font-bold text-lg mb-4">
                    2
                </div>
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-3">
                    {{ __('web/pages/about.how_it_works.steps.discover.title') }}
                </h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('web/pages/about.how_it_works.steps.discover.description') }}
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.discover.features.complete_info') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.discover.features.photos') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.discover.features.practical') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Étape 3 : Contribution -->
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 font-bold text-lg mb-4">
                    3
                </div>
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-3">
                    {{ __('web/pages/about.how_it_works.steps.contribute.title') }}
                </h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    {{ __('web/pages/about.how_it_works.steps.contribute.description') }}
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.contribute.features.propose') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.contribute.features.improve') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('web/pages/about.how_it_works.steps.contribute.features.moderation') }}</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</section>
