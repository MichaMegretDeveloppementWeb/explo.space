<!-- Section Philosophie -->
<section id="philosophy" class="bg-white py-12 sm:py-16 md:py-20 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Titre et sous-titre -->
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                {{ __('web/pages/about.philosophy.title') }}
            </h2>
            <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                {{ __('web/pages/about.philosophy.subtitle') }}
            </p>
        </div>

        <!-- Valeurs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

            <!-- Valeur 1 : Fonctionnel -->
            <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('web/pages/about.philosophy.values.functional.title') }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('web/pages/about.philosophy.values.functional.description') }}
                </p>
            </div>

            <!-- Valeur 2 : Gratuit -->
            <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('web/pages/about.philosophy.values.free.title') }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('web/pages/about.philosophy.values.free.description') }}
                </p>
            </div>

            <!-- Valeur 3 : Collaboratif -->
            <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600 mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('web/pages/about.philosophy.values.collaborative.title') }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('web/pages/about.philosophy.values.collaborative.description') }}
                </p>
            </div>

            <!-- Valeur 4 : Qualité -->
            <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('web/pages/about.philosophy.values.quality.title') }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('web/pages/about.philosophy.values.quality.description') }}
                </p>
            </div>

            <!-- Valeur 5 : Vie privée -->
            <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-600 mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('web/pages/about.philosophy.values.privacy.title') }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('web/pages/about.philosophy.values.privacy.description') }}
                </p>
            </div>

            <!-- Valeur 6 : Durable -->
            <div class="bg-gray-50 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-teal-100 text-teal-600 mb-4">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ __('web/pages/about.philosophy.values.sustainable.title') }}
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('web/pages/about.philosophy.values.sustainable.description') }}
                </p>
            </div>

        </div>

    </div>
</section>
