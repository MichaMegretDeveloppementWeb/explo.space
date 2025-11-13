<!-- Section Pourquoi explo.space -->
<section class="py-12 sm:py-16 md:py-20 lg:py-32 bg-white px-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 sm:gap-16 items-center">
            <!-- Contenu -->
            <div class="text-center lg:text-left">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4 sm:mb-6 px-2 lg:px-0">
                    {{ __('web/pages/home.why_cosmap.title') }}
                </h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 mb-6 sm:mb-8 px-2 lg:px-0 max-w-xl mx-auto">
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
                <!-- Mockup : Page d'exploration skeleton premium -->
                <div class="relative mx-auto w-full sm:w-[90%] lg:w-[85%] max-w-xs sm:max-w-md lg:max-w-lg">
                    <!-- Container avec hauteur fixe pour stabiliser la page -->
                    <div class="h-[580px] flex items-center justify-center">
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 relative overflow-hidden w-full"
                             style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02);">

                            <!-- Gradient background subtil -->
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-white to-green-50/20 pointer-events-none"></div>

                            <!-- Contenu relatif -->
                            <div class="relative z-10">

                        <!-- Header avec recherche premium -->
                        <div class="mb-0 p-3 sm:p-4 bg-white rounded-xl border border-gray-200"
                             style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">
                            <div class="flex items-center space-x-2 sm:space-x-3 mb-3">
                                <!-- Champ de recherche amélioré -->
                                <div class="flex-1 bg-gray-50 rounded-lg border border-gray-200 px-2.5 sm:px-3 py-2 sm:py-2.5">
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <div class="h-2 sm:h-2.5 w-16 sm:w-20 bg-gray-300 rounded"></div>
                                    </div>
                                </div>
                                <!-- Bouton géolocalisation premium -->
                                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors"
                                     style="box-shadow: 0 2px 6px -1px rgba(59,130,246,0.3);">
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

                            <div class="h-32 sm:h-40 rounded-lg relative overflow-hidden border border-gray-200 mb-3 sm:mb-4"
                                 style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">

                                <!-- Background map image -->
                                <div class="absolute inset-0 bg-cover bg-center opacity-20"
                                     style="background-image: url('{{ Vite::asset('resources/images/backgroud_map.webp') }}');"></div>

                                <!-- Marqueurs individuels SVG -->

                                <!-- France (Kourou) -->
                                <div class="absolute" style="top: 55%; left: 12%; transform: translate(-50%, -100%);">
                                    <svg width="18" height="28" viewBox="-1 0 28 41">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Chili (ALMA) -->
                                <div class="absolute" style="top: 75%; left: 15%; transform: translate(-50%, -100%);">
                                    <svg width="18" height="28" viewBox="-1 0 28 41">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Japon -->
                                <div class="absolute" style="top: 35%; left: 82%; transform: translate(-50%, -100%);">
                                    <svg width="18" height="28" viewBox="-1 0 28 41">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Australie -->
                                <div class="absolute" style="top: 75%; left: 78%; transform: translate(-50%, -100%);">
                                    <svg width="18" height="28" viewBox="-1 0 28 41">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Clusters -->

                                <!-- Cluster États-Unis (NASA/SpaceX) -->
                                <div class="absolute" style="top: 32%; left: 18%;">
                                    <div class="marker-cluster marker-cluster-medium">12</div>
                                </div>

                                <!-- Cluster Europe (ESA + observatoires) -->
                                <div class="absolute" style="top: 24%; left: 44%;">
                                    <div class="marker-cluster marker-cluster-medium">8</div>
                                </div>

                                <!-- Cluster Russie/Kazakhstan (Baïkonour + autres) -->
                                <div class="absolute" style="top: 25%; left: 65%;">
                                    <div class="marker-cluster marker-cluster-medium">6</div>
                                </div>

                                <!-- Cluster Asie-Pacifique (Chine, Inde, Corée) -->
                                <div class="absolute" style="top: 40%; left: 72%;">
                                    <div class="marker-cluster marker-cluster-small">4</div>
                                </div>

                                <!-- Cluster Amérique du Sud (Brésil + Chili) -->
                                <div class="absolute" style="top: 65%; left: 16%;">
                                    <div class="marker-cluster marker-cluster-small">3</div>
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
        </div>
    </div>
</section>
