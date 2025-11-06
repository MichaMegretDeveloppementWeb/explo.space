<!-- Section Contribution communautaire -->
<section class="py-12 sm:py-16 md:py-20 bg-white">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="text-center mb-12 sm:mb-20 md:mb-32">
            <div class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-purple-100 text-purple-800 text-xs sm:text-sm font-medium mb-3 sm:mb-4">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-purple-500 rounded-full mr-1.5 sm:mr-2"></span>
                {{ __('web/pages/home.community_contribution.badge') }}
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 px-2 sm:px-0">
                {{ __('web/pages/home.community_contribution.title') }}
            </h2>
            <p class="mt-3 sm:mt-4 text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                {{ __('web/pages/home.community_contribution.subtitle') }}
            </p>
        </div>

        <!-- Grid de contribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-16 gap-x-16 sm:gap-x-24 lg:gap-x-32 items-center">

            <div class="flex flex-col items-start justify-start self-stretch order-2 lg:order-1">

                <!-- Contribution 1: Proposer des lieux -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col lg:flex-row items-start lg:items-start space-y-4 lg:space-y-0 lg:space-x-4 mb-4 sm:mb-6">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md sm:shadow-lg lg:translate-y-[-15%] bg-white border border-gray-100">
                            <x-heroicon-o-plus class="w-5 h-5 sm:w-6 sm:h-6 text-green-500" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 px-2 lg:px-0">{{ __('web/pages/home.community_contribution.actions.propose_places.title') }}</h3>
                            <p class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4 px-2 lg:px-0 leading-relaxed">
                                {{ __('web/pages/home.community_contribution.actions.propose_places.description') }}
                            </p>
                            <ul class="space-y-2 sm:space-y-3">
                                <li class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-check class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" />
                                    </div>
                                    <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.community_contribution.actions.propose_places.benefits.simple_form') }}</span>
                                </li>
                                <li class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-check class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" />
                                    </div>
                                    <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.community_contribution.actions.propose_places.benefits.expert_validation') }}</span>
                                </li>
                                <li class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-check class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" />
                                    </div>
                                    <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.community_contribution.actions.propose_places.benefits.automatic_publication') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mockup proposition lieu -->
                <div class="w-full my-auto order-1 lg:order-2">
                    <div class="relative mx-auto w-full sm:w-[90%] lg:w-[85%] max-w-xs sm:max-w-md">

                        <div class="rounded-xl sm:rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 shadow-lg sm:shadow-xl">
                            <!-- Header formulaire -->
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <h4 class="text-sm sm:text-base font-semibold text-gray-900">{{ __('web/pages/home.community_contribution.actions.propose_places.mockup.form_title') }}</h4>
                                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <x-heroicon-s-plus class="w-3 h-3 sm:w-4 sm:h-4 text-green-600" />
                                </div>
                            </div>

                            <!-- Champs du formulaire mockup -->
                            <div class="space-y-3 sm:space-y-4">
                                <div>
                                    <div class="h-2.5 sm:h-3 w-12 sm:w-16 bg-gray-300 rounded mb-1.5 sm:mb-2"></div>
                                    <div class="h-8 sm:h-10 bg-gray-100 rounded-lg flex items-center px-2.5 sm:px-3">
                                        <div class="h-2.5 sm:h-3 w-24 sm:w-32 bg-green-400 rounded"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="h-2.5 sm:h-3 w-16 sm:w-20 bg-gray-300 rounded mb-1.5 sm:mb-2"></div>
                                    <div class="h-16 sm:h-20 bg-gray-100 rounded-lg p-2.5 sm:p-3">
                                        <div class="space-y-1.5 sm:space-y-2">
                                            <div class="h-1.5 sm:h-2 w-full bg-gray-300 rounded"></div>
                                            <div class="h-1.5 sm:h-2 w-3/4 bg-gray-300 rounded"></div>
                                            <div class="h-1.5 sm:h-2 w-1/2 bg-gray-300 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="h-2.5 sm:h-3 w-18 sm:w-24 bg-gray-300 rounded mb-1.5 sm:mb-2"></div>
                                    <div class="h-16 sm:h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-lg relative overflow-hidden border border-gray-200">
                                        <!-- Mini carte adaptée mobile -->
                                        <div class="absolute top-1.5 sm:top-2 left-1.5 sm:left-2 w-2.5 sm:w-3 h-1.5 sm:h-2 bg-gray-300 rounded opacity-60"></div>
                                        <div class="absolute top-2 sm:top-3 right-1.5 sm:right-2 w-3 sm:w-4 h-2 sm:h-3 bg-gray-300 rounded opacity-60"></div>
                                        <div class="absolute bottom-1.5 sm:bottom-2 left-2 sm:left-3 w-1.5 sm:w-2 h-1.5 sm:h-2 bg-gray-300 rounded opacity-60"></div>
                                        <!-- Pin de localisation -->
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                            <div class="w-2.5 sm:w-3 h-2.5 sm:h-3 bg-green-500 rounded-full border border-white sm:border-2 shadow-md sm:shadow-lg animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status en attente -->
                            <div class="mt-3 sm:mt-4 p-2.5 sm:p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center space-x-1.5 sm:space-x-2">
                                    <div class="w-3 h-3 sm:w-4 sm:h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full"></div>
                                    </div>
                                    <span class="text-xs text-yellow-800 font-medium">{{ __('web/pages/home.community_contribution.actions.propose_places.mockup.status_pending') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            <div class="flex flex-col items-start justify-start self-stretch order-4 lg:order-3">

                <!-- Contribution 2: Améliorer l'existant -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col lg:flex-row items-start lg:items-start space-y-4 lg:space-y-0 lg:space-x-4 mb-4 sm:mb-6">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md sm:shadow-lg lg:translate-y-[-15%] bg-white border border-gray-100">
                            <x-heroicon-o-pencil-square class="w-5 h-5 sm:w-6 sm:h-6 text-orange-500" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 px-2 lg:px-0">{{ __('web/pages/home.community_contribution.actions.improve_info.title') }}</h3>
                            <p class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4 px-2 lg:px-0 leading-relaxed">
                                {{ __('web/pages/home.community_contribution.actions.improve_info.description') }}
                            </p>
                            <ul class="space-y-2 sm:space-y-3">
                                <li class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-check class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-orange-600" />
                                    </div>
                                    <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.community_contribution.actions.improve_info.benefits.error_reporting') }}</span>
                                </li>
                                <li class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-check class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-orange-600" />
                                    </div>
                                    <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.community_contribution.actions.improve_info.benefits.additional_info') }}</span>
                                </li>
                                <li class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-check class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-orange-600" />
                                    </div>
                                    <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.community_contribution.actions.improve_info.benefits.transparent_moderation') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mockup modification -->
                <div class="w-full my-auto order-3 lg:order-4">
                    <div class="relative mx-auto w-full sm:w-[90%] lg:w-[85%] max-w-xs sm:max-w-md">

                        <div class="rounded-xl sm:rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 shadow-lg sm:shadow-xl">
                            <!-- Fiche lieu existante -->
                            <div class="mb-3 sm:mb-4">
                                <div class="flex items-center justify-between mb-2 sm:mb-3">
                                    <h4 class="text-sm sm:text-base font-semibold text-gray-900">{{ __('web/pages/home.community_contribution.actions.improve_info.mockup.place_title') }}</h4>
                                    <div class="w-5 h-5 sm:w-6 sm:h-6 bg-orange-100 rounded-full flex items-center justify-center">
                                        <x-heroicon-s-pencil class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-orange-600" />
                                    </div>
                                </div>

                                <!-- Contenu existant -->
                                <div class="space-y-2 sm:space-y-3">
                                    <div class="flex items-center justify-between p-1.5 sm:p-2 bg-gray-50 rounded text-xs sm:text-sm">
                                        <span class="text-gray-600">{{ __('web/pages/home.community_contribution.actions.improve_info.mockup.field_coordinates') }}</span>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-red-400 rounded-full animate-pulse"></div>
                                    </div>
                                    <div class="flex items-center justify-between p-1.5 sm:p-2 bg-gray-50 rounded text-xs sm:text-sm">
                                        <span class="text-gray-600">{{ __('web/pages/home.community_contribution.actions.improve_info.mockup.field_practical_info') }}</span>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-yellow-400 rounded-full"></div>
                                    </div>
                                    <div class="flex items-center justify-between p-1.5 sm:p-2 bg-gray-50 rounded text-xs sm:text-sm">
                                        <span class="text-gray-600">{{ __('web/pages/home.community_contribution.actions.improve_info.mockup.field_photos') }}</span>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-400 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Suggestion de modification -->
                            <div class="p-2.5 sm:p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                <div class="flex items-start space-x-1.5 sm:space-x-2">
                                    <x-heroicon-s-exclamation-triangle class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-orange-600 mt-0.5 flex-shrink-0" />
                                    <div class="flex-1">
                                        <p class="text-xs text-orange-800 font-medium">{{ __('web/pages/home.community_contribution.actions.improve_info.mockup.suggestion_title') }}</p>
                                        <p class="text-xs text-orange-700 mt-0.5 sm:mt-1 leading-relaxed">{{ __('web/pages/home.community_contribution.actions.improve_info.mockup.suggestion_example') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

</section>
