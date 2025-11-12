<!-- Hero Section - Page À propos -->
<section class="bg-white py-12 sm:py-16 md:py-18 lg:pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">

            <!-- Badge -->
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-600 text-sm font-medium mb-4">
                {{ __('web/pages/about.hero.badge') }}
            </div>

            <!-- Titre principal -->
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-4 sm:mb-6">
                <span class="block">{{ __('web/pages/about.hero.title.part1') }}</span>
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent block">
                    {{ __('web/pages/about.hero.title.part2') }}
                </span>
            </h1>

            <!-- Sous-titre -->
            <p class="text-lg sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto leading-relaxed px-2 sm:px-0">
                {{ __('web/pages/about.hero.subtitle') }}
            </p>

        </div>
    </div>
</section>
