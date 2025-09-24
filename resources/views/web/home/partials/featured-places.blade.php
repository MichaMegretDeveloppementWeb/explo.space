<!-- Section Lieux emblematiques -->
<section class="py-12 sm:py-16 md:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <div class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-blue-100 text-blue-800 text-xs sm:text-sm font-medium mb-3 sm:mb-4">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full mr-1.5 sm:mr-2"></span>
                Lieux emblématiques
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                Des destinations extraordinaires
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                Découvrez quelques-uns des lieux les plus fascinants de notre collection mondiale
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Lieu 1 -->
            <div class="group bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden hover:shadow-xl sm:hover:shadow-2xl transition-all duration-300">
                <!-- Image : Centre spatial Kennedy, vue aerienne, 400x250px -->
                <div class="h-40 sm:h-48 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center relative overflow-hidden">
                    <img src="{{ Vite::asset('resources/images/home/featured-places/kennedy_space_center.jpg') }}" alt="Centre spacial Kennedy" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-white/90 text-blue-600 text-xs font-medium rounded-full">NASA</span>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Centre spatial Kennedy</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-3 sm:mb-4">Centre de lancement historique de la NASA en Floride, berceau des missions Apollo et des navettes spatiales.</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs sm:text-sm text-gray-500">Floride, USA</span>
                        <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 group-hover:translate-x-1 transition-transform" />
                    </div>
                </div>
            </div>

            <!-- Lieu 2 -->
            <div class="group bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden hover:shadow-xl sm:hover:shadow-2xl transition-all duration-300">
                <!-- Image : Baikonour, rampe de lancement Soyouz, 400x250px -->
                <div class="h-40 sm:h-48 bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center relative overflow-hidden">
                    <img src="{{ Vite::asset('resources/images/home/featured-places/cosmodrome_baikonour.jpg') }}" alt="Cosmodrome de Baikonour" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-white/90 text-red-600 text-xs font-medium rounded-full">Roscosmos</span>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Cosmodrome de Baikonour</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-3 sm:mb-4">Premier cosmodrome au monde, site historique du vol de Gagarine et base actuelle des missions Soyouz.</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs sm:text-sm text-gray-500">Kazakhstan</span>
                        <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 group-hover:translate-x-1 transition-transform" />
                    </div>
                </div>
            </div>

            <!-- Lieu 3 -->
            <div class="group bg-white rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg overflow-hidden hover:shadow-xl sm:hover:shadow-2xl transition-all duration-300">
                <!-- Image : Observatoire ALMA, desert Atacama, 400x250px -->
                <div class="h-40 sm:h-48 bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center relative overflow-hidden">
                    <img src="{{ Vite::asset('resources/images/home/featured-places/observatoire_alma.jpg') }}" alt="Observatoire Alma" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-white/90 text-purple-600 text-xs font-medium rounded-full">Observatoire</span>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Observatoire ALMA</h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-3 sm:mb-4">Plus grand projet astronomique au monde, 66 antennes dans le désert d'Atacama pour sonder l'univers lointain.</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs sm:text-sm text-gray-500">Chili</span>
                        <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 group-hover:translate-x-1 transition-transform" />
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-8 sm:mt-12">
            <a href="#" class="inline-flex items-center px-6 sm:px-8 py-3 sm:py-4 bg-gray-900 text-white font-semibold rounded-lg sm:rounded-xl hover:bg-gray-800 transition-colors text-sm sm:text-base">
                Voir tous les lieux emblématiques
                <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 ml-2" />
            </a>
        </div>
    </div>
</section>
