<!-- Section Fonctionnalites principales -->
<section class="py-12 sm:py-16 md:py-20 bg-white px-3 sm:mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-[5em] sm:mb-20">
            <h2 class="text-2xl sm:text-4xl md:text-4xl font-bold text-gray-900">
                {{ __('web/pages/home.features.title') }}
            </h2>
            <p class="mt-3 sm:mt-4 text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                {{ __('web/pages/home.features.subtitle') }}
            </p>
        </div>

        <!-- Fonctionnalites Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-16 gap-y-16 sm:gap-y-24 lg:gap-y-32 items-center">

            <!-- Fonctionnalite 1: Autour de moi - VERSION PREMIUM -->
            <div class="order-2 lg:order-1">
                <div class="relative mx-auto w-full sm:w-[90%] md:w-[85%] max-w-sm sm:max-w-md">
                    <!-- Container principal avec glassmorphism et hauteur fixe -->
                    <div class="h-[680px] flex items-start justify-center lg:items-center">

                        <!-- Gradient background subtil -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-white to-purple-50/20 pointer-events-none w-full"></div>

                        <!-- Contenu relatif -->
                        <div class="relative z-10 rounded-2xl border border-gray-200/60 bg-white p-5 sm:p-6 overflow-hidden w-full">

                            <!-- Barre de recherche premium avec glassmorphism -->
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="h-12 bg-white border border-gray-200 rounded-xl flex items-center px-4 transition-all duration-300 hover:border-blue-300 hover:shadow-sm"
                                         style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">
                                        <!-- Icône GPS avec pulse -->
                                        <div class="relative mr-3">
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-40"></div>
                                        </div>
                                        <!-- Placeholder animé -->
                                        <div class="flex-1">
                                            <span class="text-sm text-gray-400 search-placeholder">Paris, France</span>
                                        </div>
                                        <!-- Bouton géolocalisation -->
                                        <button class="ml-2 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200"
                                                style="box-shadow: 0 2px 8px -2px rgba(59,130,246,0.4);">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Curseur rayon sophistiqué avec glassmorphism -->
                            <div class="mb-4 p-4 rounded-xl backdrop-blur-sm relative"
                                 style="background: linear-gradient(135deg, rgba(59,130,246,0.08) 0%, rgba(147,197,253,0.12) 100%); border: 1px solid rgba(59,130,246,0.2); box-shadow: 0 4px 16px -4px rgba(59,130,246,0.15);">

                                <div class="mb-4">
                                    <span class="text-xs font-semibold text-blue-900">{{ __('web/pages/home.features.modes.proximity.mockup.radius_label') }}</span>
                                </div>

                                <!-- Rail du curseur avec gradient - padding pour éviter la coupure -->
                                <div class="relative px-3">
                                    <div class="relative h-2 rounded-full"
                                         style="background: linear-gradient(90deg, #bfdbfe 0%, #3b82f6 100%);">
                                        <!-- Poignée 3D avec ombre portée - centré verticalement -->
                                        <div class="absolute h-5 w-5 bg-white rounded-full top-1/2 transform -translate-x-1/2 -translate-y-1/2 cursor-grab active:cursor-grabbing radius-slider transition-all duration-200"
                                             style="left: 50%; animation: slideRadius 10s infinite ease-in-out; box-shadow: 0 3px 8px -1px rgba(59,130,246,0.4), 0 0 0 3px rgba(59,130,246,0.15), inset 0 1px 0 rgba(255,255,255,0.8);">
                                            <div class="absolute inset-0.5 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Carte premium avec image réelle -->
                            <div class="h-64 rounded-xl relative overflow-hidden border border-gray-200"
                                 style="box-shadow: 0 8px 24px -6px rgba(0,0,0,0.08), inset 0 0 0 1px rgba(255,255,255,0.5);">

                                <!-- Image de fond : Carte réaliste -->
                                <div class="absolute inset-0 bg-cover bg-center opacity-15" style="background-image: url('{{ Vite::asset('resources/images/backgroud_map.webp') }}'); background-size: 160%"></div>

                                <!-- Marker 1 (proche - ~35% du rayon depuis centre) - distance ~90px -->
                                <div class="absolute marker-1" style="top: 62%; left: 62%; transform: translate(-50%, -100%); animation: marker1Animation 10s infinite ease-in-out;">
                                    <svg width="27" height="42" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Marker 2 (moyen - ~60% du rayon depuis centre) - distance ~150px -->
                                <div class="absolute marker-2" style="top: 74%; left: 26%; transform: translate(-50%, -100%); animation: marker2Animation 10s infinite ease-in-out;">
                                    <svg width="27" height="42" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Marker 3 (loin - ~90% du rayon depuis centre) - distance ~230px -->
                                <div class="absolute marker-3" style="top: 16%; left: 80%; transform: translate(-50%, -100%); animation: marker3Animation 10s infinite ease-in-out;">
                                    <svg width="27" height="42" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                              fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                        <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                    </svg>
                                </div>

                                <!-- Point utilisateur avec pulse premium -->
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
                                    <div class="relative">
                                        <div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white"
                                             style="box-shadow: 0 2px 8px rgba(59,130,246,0.5), 0 0 0 4px rgba(59,130,246,0.2);"></div>
                                        <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-50"></div>
                                    </div>
                                </div>

                                <!-- Cercle rayon avec effet glow -->
                                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
                                    <div class="border-2 border-blue-400/60 rounded-full radius-circle"
                                         style="width: 60px; height: 60px; animation: radiusAnimation 10s ease-in-out infinite; background: rgba(0,81,255,0.16); box-shadow: 0 0 20px rgba(59,130,246,0.15);"></div>
                                </div>

                            </div>

                            <!-- Mini-liste de résultats premium - apparaissent selon rayon -->
                            <div class="mt-4 space-y-2">
                                <!-- Result card 1 - apparait en premier (rayon moyen) -->
                                <div class="result-card-1 grid transition-all duration-300"
                                     style="animation: resultCard1Animation 10s infinite ease-in-out;">
                                    <div class="overflow-hidden">
                                        <div class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-200 transition-all duration-200"
                                             style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">
                                            <!-- Skeleton image -->
                                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="h-2.5 w-28 bg-gray-300 rounded mb-1.5"></div>
                                                <div class="h-2 w-20 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="px-2 py-1 bg-blue-50 rounded">
                                                <div class="h-3 w-10 bg-blue-200 rounded"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Result card 2 - apparait en second (rayon plus grand) -->
                                <div class="result-card-2 grid transition-all duration-300"
                                     style="animation: resultCard2Animation 10s infinite ease-in-out;">
                                    <div class="overflow-hidden">
                                        <div class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-200 transition-all duration-200"
                                             style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">
                                            <!-- Skeleton image -->
                                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="h-2.5 w-32 bg-gray-300 rounded mb-1.5"></div>
                                                <div class="h-2 w-24 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="px-2 py-1 bg-blue-50 rounded">
                                                <div class="h-3 w-10 bg-blue-200 rounded"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Result card 3 - apparait en dernier (rayon max) -->
                                <div class="result-card-3 grid transition-all duration-300"
                                     style="animation: resultCard3Animation 10s infinite ease-in-out;">
                                    <div class="overflow-hidden">
                                        <div class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-200 transition-all duration-200"
                                             style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">
                                            <!-- Skeleton image -->
                                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="h-2.5 w-36 bg-gray-300 rounded mb-1.5"></div>
                                                <div class="h-2 w-28 bg-gray-200 rounded"></div>
                                            </div>
                                            <div class="px-2 py-1 bg-blue-50 rounded">
                                                <div class="h-3 w-10 bg-blue-200 rounded"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2 max-w-3xl mx-auto">
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                    {{ __('web/pages/home.features.modes.proximity.title') }}
                </h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-2 sm:px-0 leading-relaxed">
                    {{ __('web/pages/home.features.modes.proximity.description') }}
                </p>
                <ul class="space-y-2 sm:space-y-3">
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.features.modes.proximity.benefits.geolocation') }}</span>
                    </li>
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.features.modes.proximity.benefits.custom_radius') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Fonctionnalite 2: Recherche thematique -->
            <div class="order-3 max-w-3xl mx-auto">
                <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                    {{ __('web/pages/home.features.modes.thematic.title') }}
                </h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-2 sm:px-0 leading-relaxed">
                    {{ __('web/pages/home.features.modes.thematic.description') }}
                </p>
                <ul class="space-y-2 sm:space-y-3">
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.features.modes.thematic.benefits.themes_available') }}</span>
                    </li>
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.features.modes.thematic.benefits.worldwide_coverage') }}</span>
                    </li>
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">{{ __('web/pages/home.features.modes.thematic.benefits.smart_clustering') }}</span>
                    </li>
                </ul>
            </div>

            <div class="order-4">
                <!-- Mockup premium : Tags et carte thematique -->
                <div class="relative mx-auto w-full sm:w-[90%] md:w-[85%] max-w-sm sm:max-w-md">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 relative overflow-hidden"
                         style="box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02);">
                        <!-- Gradient background subtil -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-white to-purple-50/20 pointer-events-none"></div>

                        <!-- Tags selectionnes avec animation premium -->
                        <div class="relative z-10 mb-4">
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1.5 text-xs font-medium rounded-full border tag-nasa"
                                      style="animation: tagNasaAnimation 6s infinite; transition: all 0.3s ease;">
                                    {{ __('web/pages/home.features.modes.thematic.mockup.tag_nasa') }}
                                </span>
                                <span class="px-3 py-1.5 text-xs font-medium rounded-full border tag-apollo"
                                      style="animation: tagApolloAnimation 6s infinite; transition: all 0.3s ease;">
                                    {{ __('web/pages/home.features.modes.thematic.mockup.tag_apollo') }}
                                </span>
                                <span class="px-3 py-1.5 bg-gray-50 text-gray-500 text-xs font-medium rounded-full border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer">
                                    SpaceX
                                </span>
                                <div class="px-2.5 py-1.5 border-2 border-dashed border-gray-300 text-gray-400 text-xs font-medium rounded-full flex items-center hover:border-gray-400 transition-colors cursor-pointer">
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mr-1.5"></div>
                                    <span>+3</span>
                                </div>
                            </div>
                        </div>

                        <!-- Carte mondiale avec background image et points -->
                        <div class="relative z-10 h-64 rounded-xl relative overflow-hidden border border-gray-200"
                             style="box-shadow: 0 4px 12px -2px rgba(0,0,0,0.05);">

                            <!-- Background map image -->
                            <div class="absolute inset-0 bg-cover bg-center opacity-20"
                                 style="background-image: url('{{ Vite::asset('resources/images/backgroud_map.webp') }}');"></div>

                            <!-- NASA Points (3 clusters + 5 markers) - Visible de 0 à 3s -->
                            <!-- Cluster 1 NASA -->
                            <div class="nasa-points absolute" style="top: 20%; left: 15%; animation: nasaPointsAnimation 6s infinite;">
                                <div class="marker-cluster marker-cluster-medium">12</div>
                            </div>
                            <!-- Cluster 2 NASA -->
                            <div class="nasa-points absolute" style="top: 65%; right: 20%; animation: nasaPointsAnimation 6s infinite;">
                                <div class="marker-cluster marker-cluster-medium">8</div>
                            </div>
                            <!-- Cluster 3 NASA -->
                            <div class="nasa-points absolute" style="top: 40%; right: 15%; animation: nasaPointsAnimation 6s infinite;">
                                <div class="marker-cluster marker-cluster-small">5</div>
                            </div>

                            <!-- Marker 1 NASA -->
                            <div class="nasa-points absolute marker-1-nasa" style="top: 30%; left: 25%; animation: nasaPointsAnimation 6s infinite;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                            <!-- Marker 2 NASA -->
                            <div class="nasa-points absolute marker-2-nasa" style="top: 55%; left: 35%; animation: nasaPointsAnimation 6s infinite;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                            <!-- Marker 3 NASA -->
                            <div class="nasa-points absolute marker-3-nasa" style="top: 15%; left: 45%; animation: nasaPointsAnimation 6s infinite;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                            <!-- Marker 4 NASA -->
                            <div class="nasa-points absolute marker-4-nasa" style="top: 70%; left: 60%; animation: nasaPointsAnimation 6s infinite;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                            <!-- Marker 5 NASA -->
                            <div class="nasa-points absolute marker-5-nasa" style="top: 45%; right: 30%; animation: nasaPointsAnimation 6s infinite;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#3b82f6" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>

                            <!-- Apollo Points (4 clusters + 3 markers) - Visible de 3 à 6s -->
                            <!-- Cluster 1 Apollo -->
                            <div class="apollo-points absolute" style="top: 25%; left: 20%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="marker-cluster marker-cluster-medium marker-cluster-purple">7</div>
                            </div>
                            <!-- Cluster 2 Apollo -->
                            <div class="apollo-points absolute" style="top: 60%; left: 40%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="marker-cluster marker-cluster-medium marker-cluster-purple">11</div>
                            </div>
                            <!-- Cluster 3 Apollo -->
                            <div class="apollo-points absolute" style="top: 35%; right: 25%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="marker-cluster marker-cluster-small marker-cluster-purple">4</div>
                            </div>
                            <!-- Cluster 4 Apollo -->
                            <div class="apollo-points absolute" style="top: 15%; right: 15%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="marker-cluster marker-cluster-small marker-cluster-purple">6</div>
                            </div>

                            <!-- Marker 1 Apollo -->
                            <div class="apollo-points absolute marker-1-apollo" style="top: 50%; left: 25%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#a855f7" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                            <!-- Marker 2 Apollo -->
                            <div class="apollo-points absolute marker-2-apollo" style="top: 70%; right: 35%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#a855f7" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                            <!-- Marker 3 Apollo -->
                            <div class="apollo-points absolute marker-3-apollo" style="top: 25%; left: 55%; animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <svg width="27" height="42" viewBox="-1 0 28 41">
                                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                                          fill="#a855f7" stroke="#ffffff" stroke-width="1.5"/>
                                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Compteur resultats avec animation premium -->
                        <div class="relative z-10 mt-4">
                            <div class="text-center px-4 py-2.5 bg-white rounded-lg border border-gray-200"
                                 style="box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04);">
                                <span class="text-sm font-medium text-gray-700 results-counter"
                                      style="animation: resultsAnimation 6s infinite;"
                                      data-nasa-text="{{ str_replace(':count', '30', __('web/pages/home.features.modes.thematic.mockup.results_nasa')) }}"
                                      data-apollo-text="{{ str_replace(':count', '31', __('web/pages/home.features.modes.thematic.mockup.results_apollo')) }}">
                                    <span class="results-content">{{ str_replace(':count', '30', __('web/pages/home.features.modes.thematic.mockup.results_nasa')) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


