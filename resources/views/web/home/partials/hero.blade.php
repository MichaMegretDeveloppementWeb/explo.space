@push('head')
    <!-- Preload hero image for better performance -->
    <link rel="preload" as="image" href="{{ Vite::asset('resources/images/home/hero/terre-vue-espace.png') }}">
@endpush

<!-- Hero Section - Modern style with background image -->
<section class="relative bg-white py-20 sm:py-24 md:py-32 lg:py-40 overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img
            src="{{ Vite::asset('resources/images/home/hero/terre-vue-espace.png') }}"
            alt="Image de la terre vue du ciel"
            class="w-full h-full object-cover"
        >
        <!-- Dark overlay with gradient for better text contrast -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900/85 via-blue-900/75 to-gray-900/85"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">

            <!-- Titre principal -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-4 sm:mb-6">
                <span class="block sm:inline text-white">{{ __('web/pages/home.hero.title.part1') }}</span>
                <span class="block sm:inline bg-gradient-to-r from-blue-400 via-cyan-300 to-blue-500 bg-clip-text text-transparent">
                    {{ __('web/pages/home.hero.title.part2') }}
                </span>
            </h1>

            <!-- Sous-titre -->
            <p class="text-lg sm:text-xl md:text-2xl text-white/90 max-w-2xl sm:max-w-3xl mx-auto leading-relaxed mb-8 sm:mb-10 md:mb-12 px-2 sm:px-0">
                <span class="block sm:inline">{{ __('web/pages/home.hero.subtitle.part1') }}</span>
                <span class="block sm:inline">{{ __('web/pages/home.hero.subtitle.part2') }}</span>
                <span class="hidden sm:block sm:mt-1">{{ __('web/pages/home.hero.subtitle.part3') }}</span>
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center mb-16 sm:mb-20 md:mb-24 px-4 sm:px-0">
                <a href="{{ localRoute('explore') }}" class="bg-blue-600 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/50 w-full sm:w-auto">
                    {{ __('web/pages/home.hero.cta.primary') }}
                </a>
                <a href="{{ localRoute('place_requests.create') }}" class="border-2 border-white/90 hover:border-white text-white hover:bg-white/10 px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold transition-all backdrop-blur-sm w-full sm:w-auto">
                    {{ __('web/pages/home.hero.cta.secondary') }}
                </a>
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap justify-center gap-8 sm:gap-12 lg:gap-16 max-w-5xl mx-auto">
                <div class="text-center min-w-[140px]">
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent mb-2">
                        {{ $stats['places_count'] }}
                    </div>
                    <div class="text-base sm:text-lg text-white font-medium">
                        {{ __('web/pages/home.hero.stats.places.label') }}
                    </div>
                </div>
                <div class="text-center min-w-[140px]">
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-purple-400 to-pink-300 bg-clip-text text-transparent mb-2">
                        {{ $stats['featured_places_count'] }}
                    </div>
                    <div class="text-base sm:text-lg text-white font-medium">
                        {{ __('web/pages/home.hero.stats.featured.label') }}
                    </div>
                </div>
                <div class="text-center min-w-[140px]">
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-bold bg-gradient-to-r from-green-400 to-teal-300 bg-clip-text text-transparent mb-2">
                        {{ $stats['active_tags_count'] }}
                    </div>
                    <div class="text-base sm:text-lg text-white font-medium">
                        {{ __('web/pages/home.hero.stats.themes.label') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
