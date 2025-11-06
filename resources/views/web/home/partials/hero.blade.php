<!-- Hero Section - Style SaaS moderne -->
<section class="bg-white py-12 sm:py-16 md:py-18 lg:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">

            <!-- Titre principal -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-4 sm:mb-6">
                <span class="block sm:inline">{{ __('web/pages/home.hero.title.part1') }}</span>
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent block sm:inline">
                    {{ __('web/pages/home.hero.title.part2') }}
                </span>
            </h1>

            <!-- Sous-titre -->
            <p class="text-lg sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto leading-relaxed mb-6 sm:mb-8 md:mb-10 px-2 sm:px-0">
                <span class="block sm:inline">{{ __('web/pages/home.hero.subtitle.part1') }}</span>
                <span class="block sm:inline">{{ __('web/pages/home.hero.subtitle.part2') }}</span>
                <span class="hidden sm:block sm:mt-1">{{ __('web/pages/home.hero.subtitle.part3') }}</span>
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center mb-8 sm:mb-12 md:mb-16 px-4 sm:px-0">
                <a href="{{ localRoute('explore') }}" class="bg-blue-600 text-white px-6 sm:px-8 py-2 rounded-lg text-base sm:text-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/25 w-full sm:w-auto">
                    {{ __('web/pages/home.hero.cta.primary') }}
                </a>
                <a href="{{ localRoute('place_requests.create') }}" class="border border-blue-500 hover:border-blue-700 text-blue-500 hover:text-blue-600 px-6 sm:px-8 py-2 rounded-lg text-base sm:text-lg font-semibold transition-colors w-full sm:w-auto">
                    {{ __('web/pages/home.hero.cta.secondary') }}
                </a>
            </div>

            <!-- Hero Image -->
            <div class="relative w-full mx-auto my-8 sm:my-12 md:my-16">
                <!-- Image : Dashboard/Interface explo.space avec carte et resultats, 1200x600px -->
                <img
                    src="{{ Vite::asset('resources/images/home/hero/terre-vue-espace.png') }}"
                     alt="Image de la terre vue du ciel"
                     class="aspect-16/9 w-full rounded-lg sm:rounded-2xl shadow-[0px_10px_60px_#00000026]"
                >
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap items-start justify-around gap-3 mt-16 sm:mt-12 md:mt-16 lg:mt-30 max-w-xl sm:max-w-none mx-auto">
                <div class="text-center py-4 sm:py-0 min-w-[10em]">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-semibold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ __('web/pages/home.hero.stats.places.count') }}</div>
                    <div class="text-sm sm:text-base text-gray-600 mt-1">{{ __('web/pages/home.hero.stats.places.label') }}</div>
                </div>
                <div class="text-center py-4 sm:py-0 min-w-[10em]">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-semibold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ __('web/pages/home.hero.stats.countries.count') }}</div>
                    <div class="text-sm sm:text-base text-gray-600 mt-1">{{ __('web/pages/home.hero.stats.countries.label') }}</div>
                </div>
                <div class="text-center py-4 sm:py-0 min-w-[10em]">
                    <div class="text-2xl sm:text-3xl lg:text-4xl font-semibold bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent">{{ __('web/pages/home.hero.stats.categories.count') }}</div>
                    <div class="text-sm sm:text-base text-gray-600 mt-1">{{ __('web/pages/home.hero.stats.categories.label') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
