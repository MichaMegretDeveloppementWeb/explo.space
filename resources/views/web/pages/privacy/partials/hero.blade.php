{{-- Hero Section --}}
<section class="relative bg-gradient-to-br from-gray-50 to-white pt-20 pb-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        {{-- Badge --}}
        <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-100 text-blue-700 text-sm font-medium mb-6">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ __('web/pages/privacy.hero.badge') }}
        </div>

        {{-- Titre --}}
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
            {{ __('web/pages/privacy.hero.title.part1') }}
            <span class="text-blue-600">{{ __('web/pages/privacy.hero.title.part2') }}</span>
        </h1>

        {{-- Sous-titre --}}
        <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
            {{ __('web/pages/privacy.hero.subtitle') }}
        </p>
    </div>
</section>
