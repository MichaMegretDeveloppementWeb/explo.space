<!-- Section Comment ca marche -->
<section class="py-18 sm:py-20 md:py-26 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">
                {{ __('web/pages/home.how_it_works.title') }}
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                {{ __('web/pages/home.how_it_works.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-12 sm:gap-10 md:gap-12 relative">

            <!-- Etape 1 -->
            <div class="text-center">
                <div class="relative mb-6 sm:mb-8">
                    <div class="w-16 sm:w-18 md:w-20 h-16 sm:h-18 md:h-20 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto shadow-lg sm:shadow-xl">
                        <x-heroicon-o-magnifying-glass class="w-8 sm:w-9 md:w-10 h-8 sm:h-9 md:h-10 text-white" />
                    </div>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">{{ __('web/pages/home.how_it_works.steps.search.title') }}</h3>
                <p class="text-sm sm:text-base text-gray-600 px-2 sm:px-0">
                    {{ __('web/pages/home.how_it_works.steps.search.description') }}
                </p>
            </div>

            <!-- Etape 2 -->
            <div class="text-center">
                <div class="relative mb-6 sm:mb-8">
                    <div class="w-16 sm:w-18 md:w-20 h-16 sm:h-18 md:h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto shadow-lg sm:shadow-xl">
                        <x-heroicon-o-map class="w-8 sm:w-9 md:w-10 h-8 sm:h-9 md:h-10 text-white" />
                    </div>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">{{ __('web/pages/home.how_it_works.steps.explore.title') }}</h3>
                <p class="text-sm sm:text-base text-gray-600 px-2 sm:px-0">
                    {{ __('web/pages/home.how_it_works.steps.explore.description') }}
                </p>
            </div>

            <!-- Etape 3 -->
            <div class="text-center sm:col-span-2 md:col-span-1">
                <div class="relative mb-6 sm:mb-8">
                    <div class="w-16 sm:w-18 md:w-20 h-16 sm:h-18 md:h-20 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto shadow-lg sm:shadow-xl">
                        <x-heroicon-o-information-circle class="w-8 sm:w-9 md:w-10 h-8 sm:h-9 md:h-10 text-white" />
                    </div>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">{{ __('web/pages/home.how_it_works.steps.discover.title') }}</h3>
                <p class="text-sm sm:text-base text-gray-600 px-2 sm:px-0 sm:max-w-sm md:max-w-none mx-auto">
                    {{ __('web/pages/home.how_it_works.steps.discover.description') }}
                </p>
            </div>
        </div>
    </div>
</section>
