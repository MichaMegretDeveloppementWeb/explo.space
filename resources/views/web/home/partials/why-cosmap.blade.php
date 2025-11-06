<!-- Section Pourquoi explo.space -->
<section class="py-12 sm:py-16 md:py-20 lg:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 sm:gap-16 items-center">
            <!-- Contenu -->
            <div class="text-center lg:text-left">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4 sm:mb-6 px-2 lg:px-0">
                    {{ __('web/pages/home.why_cosmap.title') }}
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 mb-6 sm:mb-8 px-2 lg:px-0">
                    {{ __('web/pages/home.why_cosmap.subtitle') }}
                </p>

                <div class="flex flex-col gap-6 md:gap-12 max-w-lg lg:max-w-none mx-auto lg:mx-0 mt-16">
                    <!-- Avantage 1 -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-3 sm:space-y-0 sm:space-x-4 text-center sm:text-left">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-check-circle class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">{{ __('web/pages/home.why_cosmap.benefits.collaborative_database.title') }}</h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ __('web/pages/home.why_cosmap.benefits.collaborative_database.description') }}</p>
                        </div>
                    </div>

                    <!-- Avantage 2 -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-3 sm:space-y-0 sm:space-x-4 text-center sm:text-left">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-bolt class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">{{ __('web/pages/home.why_cosmap.benefits.smart_search.title') }}</h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ __('web/pages/home.why_cosmap.benefits.smart_search.description') }}</p>
                        </div>
                    </div>

                    <!-- Avantage 3 -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-3 sm:space-y-0 sm:space-x-4 text-center sm:text-left">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-users class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">{{ __('web/pages/home.why_cosmap.benefits.engaged_community.title') }}</h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ __('web/pages/home.why_cosmap.benefits.engaged_community.description') }}</p>
                        </div>
                    </div>

                    <!-- Avantage 4 -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-3 sm:space-y-0 sm:space-x-4 text-center sm:text-left">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-clock class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1 sm:mb-2">{{ __('web/pages/home.why_cosmap.benefits.guaranteed_quality.title') }}</h3>
                            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ __('web/pages/home.why_cosmap.benefits.guaranteed_quality.description') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mockup/Visual -->
            <div class="relative order-1 lg:order-2">
                <!-- Mockup : Page d'exploration skeleton -->
                <div class="relative mx-auto w-full sm:w-[90%] lg:w-[85%] max-w-xs sm:max-w-md lg:max-w-lg">
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl border border-gray-200 overflow-hidden">

                        <!-- Header avec recherche -->
                        <div class="p-3 sm:p-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center space-x-2 sm:space-x-3 mb-3">
                                <!-- Champ de recherche -->
                                <div class="flex-1 bg-white rounded-lg border border-gray-300 px-2.5 sm:px-3 py-2 sm:py-2.5">
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <div class="h-2 sm:h-2.5 w-16 sm:w-20 bg-gray-300 rounded"></div>
                                    </div>
                                </div>
                                <!-- Bouton géolocalisation -->
                                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Filtres -->
                            <div class="flex items-center space-x-2">
                                <div class="bg-blue-500 text-white px-2.5 sm:px-3 py-1 rounded-full">
                                    <div class="h-1.5 sm:h-2 w-8 sm:w-10 bg-blue-200 rounded"></div>
                                </div>
                                <div class="bg-gray-200 rounded-full px-2.5 sm:px-3 py-1">
                                    <div class="h-1.5 sm:h-2 w-6 sm:w-8 bg-gray-400 rounded"></div>
                                </div>
                                <div class="bg-gray-200 rounded-full px-2.5 sm:px-3 py-1">
                                    <div class="h-1.5 sm:h-2 w-7 sm:w-9 bg-gray-400 rounded"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Carte interactive -->
                        <div class="p-3 sm:p-4">

                            <div class="h-32 sm:h-40 rounded-lg relative overflow-hidden border border-gray-300 mb-3 sm:mb-4">
                                <!-- Background dégradé moderne sur océan -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-100 via-blue-100 to-blue-100"></div>

                                <!-- Grille subtile optionnelle -->
                                <div class="absolute inset-0 opacity-30" style="background-image:
                                    linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                                    linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
                                    background-size: 25px 25px;">
                                </div>

                                <!-- SVG World Map avec continents reconnaissables -->
                                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Amérique du Nord -->
                                    <path d="M30 40 L80 30 L90 50 L85 70 L75 80 L65 85 L50 90 L35 85 L25 75 L20 60 L25 45 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Amérique du Sud -->
                                    <path d="M50 110 L65 105 L70 120 L75 140 L70 160 L60 170 L50 175 L45 165 L40 145 L45 125 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Europe -->
                                    <path d="M160 45 L180 40 L190 50 L185 65 L175 70 L165 65 L155 55 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Afrique -->
                                    <path d="M170 80 L185 75 L195 90 L200 110 L195 130 L185 145 L175 150 L165 145 L160 130 L165 110 L165 95 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Asie -->
                                    <path d="M220 30 L280 25 L300 35 L310 50 L305 70 L290 80 L270 85 L250 80 L230 70 L215 55 L210 40 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Chine/Inde -->
                                    <path d="M280 90 L310 85 L320 100 L315 115 L300 125 L285 120 L275 105 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Australie -->
                                    <path d="M300 150 L340 145 L350 155 L345 165 L330 170 L310 165 L295 160 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"
                                          class="transition-all duration-300 hover:fill-opacity-90"/>

                                    <!-- Groenland -->
                                    <path d="M120 15 L140 10 L145 20 L140 30 L130 35 L120 30 L115 20 Z"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"/>

                                    <!-- Japon -->
                                    <rect x="330" y="65" width="8" height="20" rx="2"
                                          fill="rgba(245, 243, 235, 0.85)"
                                          stroke="rgba(229, 231, 235, 0.8)"
                                          stroke-width="0.8"/>

                                    <!-- Royaume-Uni -->
                                    <ellipse cx="155" cy="50" rx="4" ry="8"
                                             fill="rgba(245, 243, 235, 0.85)"
                                             stroke="rgba(229, 231, 235, 0.8)"
                                             stroke-width="0.8"/>

                                </svg>

                                <!-- Points de données sans clignotement -->
                                <!-- États-Unis (Floride individuel) -->
                                <div class="absolute" style="top: 40%; left: 22%;">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full border-2 border-white shadow-lg"></div>
                                </div>

                                <!-- France (Kourou) -->
                                <div class="absolute" style="top: 55%; left: 12%;">
                                    <div class="w-2 h-2 bg-red-600 rounded-full border-2 border-white shadow-lg"></div>
                                </div>

                                <!-- Chili (ALMA) -->
                                <div class="absolute" style="top: 75%; left: 15%;">
                                    <div class="w-2 h-2 bg-orange-600 rounded-full border-2 border-white shadow-lg"></div>
                                </div>

                                <!-- Japon -->
                                <div class="absolute" style="top: 35%; left: 82%;">
                                    <div class="w-2 h-2 bg-yellow-600 rounded-full border-2 border-white shadow-lg"></div>
                                </div>

                                <!-- Australie -->
                                <div class="absolute" style="top: 75%; left: 78%;">
                                    <div class="w-2 h-2 bg-teal-600 rounded-full border-2 border-white shadow-lg"></div>
                                </div>

                                <!-- Clusters de points avec regroupement -->

                                <!-- Cluster États-Unis (NASA/SpaceX) -->
                                <div class="absolute" style="top: 32%; left: 18%;">
                                    <div class="relative">
                                        <div class="w-5 h-5 bg-gradient-to-r from-blue-500 to-blue-700 rounded-full border-2 border-white shadow-xl flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">12</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cluster Europe (ESA + observatoires) -->
                                <div class="absolute" style="top: 24%; left: 44%;">
                                    <div class="relative">
                                        <div class="w-5 h-5 bg-gradient-to-r from-green-500 to-green-700 rounded-full border-2 border-white shadow-xl flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">8</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cluster Russie/Kazakhstan (Baïkonour + autres) -->
                                <div class="absolute" style="top: 25%; left: 65%;">
                                    <div class="relative">
                                        <div class="w-5 h-5 bg-gradient-to-r from-purple-500 to-purple-700 rounded-full border-2 border-white shadow-xl flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">6</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cluster Asie-Pacifique (Chine, Inde, Corée) -->
                                <div class="absolute" style="top: 40%; left: 72%;">
                                    <div class="relative">
                                        <div class="w-4 h-4 bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">4</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cluster Amérique du Sud (Brésil + Chili) -->
                                <div class="absolute" style="top: 65%; left: 16%;">
                                    <div class="relative">
                                        <div class="w-4 h-4 bg-gradient-to-r from-orange-500 to-orange-700 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">3</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Interface moderne avec glass effect -->
                                <!-- Contrôles avec icônes -->
                                <div class="absolute top-2 right-2 flex gap-1">
                                    <!-- Zoom controls -->
                                    <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-sm border border-white/50 overflow-hidden">
                                        <button class="w-6 h-5 flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50/50 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                        <div class="w-6 h-px bg-gray-200/50"></div>
                                        <button class="w-6 h-5 flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50/50 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Fullscreen -->
                                    <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-sm border border-white/50 p-1">
                                        <button class="w-4 h-4 flex items-center justify-center text-gray-600 hover:text-blue-600 transition-colors">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="absolute bottom-1 left-2 text-xs text-gray-600 bg-white/80 backdrop-blur-sm px-1.5 py-0.5 rounded border border-white/30">
                                    © explo.space
                                </div>
                            </div>

                            <!-- Compteur de résultats -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-400 rounded mr-2"></div>
                                    <div class="h-2 sm:h-2.5 w-20 sm:w-24 bg-blue-500 rounded"></div>
                                </div>
                                <div class="h-2 sm:h-2.5 w-8 sm:w-10 bg-gray-300 rounded"></div>
                            </div>

                            <!-- Liste des résultats skeleton -->
                            <div class="space-y-2 sm:space-y-3">
                                <!-- Résultat actif -->
                                <div class="bg-blue-50 border-l-3 border-blue-400 rounded-r-lg p-2.5 sm:p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="h-2.5 sm:h-3 w-24 sm:w-28 bg-blue-400 rounded mb-1.5"></div>
                                            <div class="h-1.5 sm:h-2 w-16 sm:w-20 bg-blue-200 rounded"></div>
                                        </div>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full"></div>
                                    </div>
                                </div>

                                <!-- Autres résultats -->
                                <div class="bg-gray-50 rounded-lg p-2.5 sm:p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="h-2.5 sm:h-3 w-20 sm:w-24 bg-gray-300 rounded mb-1.5"></div>
                                            <div class="h-1.5 sm:h-2 w-14 sm:w-18 bg-gray-200 rounded"></div>
                                        </div>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-300 rounded-full"></div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-2.5 sm:p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="h-2.5 sm:h-3 w-22 sm:w-26 bg-gray-300 rounded mb-1.5"></div>
                                            <div class="h-1.5 sm:h-2 w-12 sm:w-16 bg-gray-200 rounded"></div>
                                        </div>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-300 rounded-full"></div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-2.5 sm:p-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="h-2.5 sm:h-3 w-18 sm:w-22 bg-gray-300 rounded mb-1.5"></div>
                                            <div class="h-1.5 sm:h-2 w-16 sm:w-20 bg-gray-200 rounded"></div>
                                        </div>
                                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-300 rounded-full"></div>
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
