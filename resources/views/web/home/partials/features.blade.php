<!-- Section Fonctionnalites principales -->
<section class="py-12 sm:py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900">
                Deux modes de recherche
            </h2>
            <p class="mt-3 sm:mt-4 text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                Decouvrez les lieux spatiaux selon vos besoins : autour de vous ou par thematique mondiale.
            </p>
        </div>

        <!-- Fonctionnalites Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 sm:gap-16 lg:gap-16 gap-y-16 sm:gap-y-24 lg:gap-y-32 items-center">

            <!-- Fonctionnalite 1: Autour de moi -->
            <div class="order-2 lg:order-1">
                <!-- Mockup skeleton : Interface de recherche "autour de moi" avec curseur rayon -->
                <div class="relative mx-auto w-full sm:w-[90%] md:w-[85%] max-w-sm sm:max-w-md">
                    <div class="rounded-xl sm:rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 shadow-lg sm:shadow-xl">
                        <!-- Barre de recherche -->
                        <div class="mb-3 sm:mb-4">
                            <div class="h-10 sm:h-12 bg-gray-100 rounded-lg flex items-center px-3 sm:px-4 relative">
                                <div class="h-3 w-3 sm:h-4 sm:w-4 bg-blue-400 rounded mr-2 sm:mr-3 flex items-center justify-center">
                                    <div class="h-1.5 w-1.5 sm:h-2 sm:w-2 bg-white rounded-full"></div>
                                </div>
                                <div class="h-2.5 sm:h-3 w-24 sm:w-32 bg-gray-300 rounded mr-2"></div>
                            </div>
                        </div>

                        <!-- Curseur rayon mis en avant avec animation -->
                        <div class="mb-3 sm:mb-4 p-2.5 sm:p-3 bg-blue-50 border-2 border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                                <span class="text-xs font-medium text-blue-800">Rayon de recherche</span>
                                <span class="text-xs font-bold text-blue-600 radius-display">750 km</span>
                            </div>
                            <div class="relative h-1.5 sm:h-2 bg-blue-200 rounded-full">
                                <div class="absolute h-3 w-3 sm:h-4 sm:w-4 bg-blue-600 rounded-full -mt-0.5 sm:-mt-1 transform -translate-x-1/2 shadow-lg radius-slider" style="left: 50%; animation: slideRadius 10s infinite;"></div>
                            </div>
                        </div>

                        <!-- Carte avec éléments cartographiques -->
                        <div class="h-36 sm:h-48 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg relative overflow-hidden border border-gray-200">
                            <!-- Grille cartographique -->
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute top-0 left-1/2 w-px h-full bg-gray-300"></div>
                                <div class="absolute left-0 top-1/2 w-full h-px bg-gray-300"></div>
                                <div class="absolute top-1/4 left-0 right-0 h-px bg-gray-200"></div>
                                <div class="absolute top-3/4 left-0 right-0 h-px bg-gray-200"></div>
                                <div class="absolute left-1/4 top-0 bottom-0 w-px bg-gray-200"></div>
                                <div class="absolute left-3/4 top-0 bottom-0 w-px bg-gray-200"></div>
                            </div>


                            <!-- Continents réalistes (identiques au mockup thématique) -->
                            <!-- Amérique du Nord -->
                            <div class="absolute top-6 left-2">
                                <div class="relative w-12 h-10 bg-gray-300 opacity-70" style="clip-path: polygon(10% 90%, 20% 80%, 30% 85%, 40% 75%, 60% 80%, 80% 70%, 90% 50%, 95% 30%, 85% 20%, 75% 10%, 60% 15%, 40% 5%, 20% 15%, 10% 25%, 5% 40%, 0% 60%, 5% 80%);">
                                    <div class="absolute inset-0 bg-gradient-to-se from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Europe / Afrique -->
                            <div class="absolute top-4 left-1/2 transform -translate-x-1/2">
                                <div class="relative w-8 h-16 bg-gray-300 opacity-70" style="clip-path: polygon(20% 0%, 60% 5%, 80% 15%, 90% 35%, 85% 50%, 90% 65%, 80% 80%, 60% 90%, 40% 95%, 20% 90%, 10% 75%, 5% 60%, 15% 45%, 10% 30%, 20% 15%);">
                                    <div class="absolute inset-0 bg-gradient-to-s from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Asie -->
                            <div class="absolute top-8 right-4">
                                <div class="relative w-14 h-12 bg-gray-300 opacity-70" style="clip-path: polygon(0% 40%, 15% 20%, 30% 25%, 50% 15%, 70% 20%, 85% 35%, 95% 50%, 90% 70%, 75% 85%, 60% 90%, 40% 95%, 25% 85%, 10% 70%, 5% 55%);">
                                    <div class="absolute inset-0 bg-gradient-to-sw from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Océanie/Australie -->
                            <div class="absolute bottom-8 right-8">
                                <div class="relative w-6 h-4 bg-gray-300 opacity-70" style="clip-path: polygon(15% 30%, 85% 20%, 95% 60%, 80% 90%, 20% 95%, 5% 70%);">
                                    <div class="absolute inset-0 bg-gradient-to-e from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Amérique du Sud -->
                            <div class="absolute bottom-12 left-6">
                                <div class="relative w-5 h-10 bg-gray-300 opacity-70" style="clip-path: polygon(40% 0%, 70% 10%, 85% 30%, 80% 50%, 85% 70%, 75% 85%, 60% 95%, 40% 90%, 25% 85%, 20% 70%, 30% 50%, 25% 30%, 35% 15%);">
                                    <div class="absolute inset-0 bg-gradient-to-s from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Points statiques (lieux) placés sur les continents -->
                            <!-- Points Amérique du Nord -->
                            <div class="absolute top-8 left-6 w-2 h-2 bg-gray-400 rounded-full"></div>
                            <div class="absolute top-12 left-8 w-2 h-2 bg-gray-400 rounded-full"></div>

                            <!-- Points Europe/Afrique -->
                            <div class="absolute top-16 left-1/2 w-2 h-2 bg-gray-400 rounded-full transform -translate-x-1/2"></div>
                            <div class="absolute top-24 left-1/2 w-2 h-2 bg-gray-400 rounded-full transform translate-x-1"></div>

                            <!-- Points Asie -->
                            <div class="absolute top-12 right-8 w-2 h-2 bg-gray-400 rounded-full"></div>
                            <div class="absolute top-16 right-12 w-2 h-2 bg-gray-400 rounded-full"></div>

                            <!-- Point Océanie -->
                            <div class="absolute bottom-8 right-10 w-2 h-2 bg-gray-400 rounded-full"></div>

                            <!-- Point Amérique du Sud -->
                            <div class="absolute bottom-16 left-8 w-2 h-2 bg-gray-400 rounded-full"></div>

                            <!-- Point central clignotant (position utilisateur) -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse shadow-lg relative">
                                    <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-75"></div>
                                    <div class="absolute inset-0.5 bg-white rounded-full"></div>
                                </div>
                            </div>

                            <!-- Cercle rayon avec animation synchronisée -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="border-2 border-blue-400 bg-[#79aaff38] border-dashed rounded-full opacity-70 radius-circle" style="width: 60px; height: 60px; animation: radiusAnimation 10s infinite;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-1 lg:order-2">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                    Recherche "Autour de moi"
                </h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-2 sm:px-0 leading-relaxed">
                    Vous etes quelque part et souhaitez decouvrir les lieux spatiaux dans un rayon defini ?
                    Utilisez notre recherche geolocalisee avec un rayon ajustable jusqu'a 1500 km.
                </p>
                <ul class="space-y-2 sm:space-y-3">
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">Geolocalisation automatique</span>
                    </li>
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">Rayon ajustable jusqu'a 1500 km</span>
                    </li>
                </ul>
            </div>

            <!-- Fonctionnalite 2: Recherche thematique -->
            <div class="order-3">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                    Recherche thematique
                </h3>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 px-2 sm:px-0 leading-relaxed">
                    Explorez tous les lieux mondiaux lies a une thematique precise : NASA, SpaceX,
                    missions Apollo, observatoires, musees spatiaux et bien plus.
                </p>
                <ul class="space-y-2 sm:space-y-3">
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">25+ thematiques disponibles</span>
                    </li>
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">Couverture mondiale</span>
                    </li>
                    <li class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-4 h-4 sm:w-5 sm:h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base text-gray-700">Clustering intelligent</span>
                    </li>
                </ul>
            </div>

            <div class="order-4">
                <!-- Mockup skeleton : Tags et carte thematique -->
                <div class="relative mx-auto w-full sm:w-[90%] md:w-[85%] max-w-sm sm:max-w-md">
                    <div class="rounded-xl sm:rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 shadow-lg sm:shadow-xl">
                        <!-- Tags selectionnes avec animation -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full border shadow-sm tag-nasa" style="animation: tagNasaAnimation 6s infinite;">NASA</span>
                                <span class="px-3 py-1 text-xs rounded-full border transition-colors cursor-pointer tag-apollo" style="animation: tagApolloAnimation 6s infinite;">Apollo</span>
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs rounded-full border border-gray-200 hover:bg-gray-200 transition-colors cursor-pointer">SpaceX</span>
                                <div class="px-2 py-1 border-2 border-dashed border-gray-300 text-gray-400 text-xs rounded-full flex items-center">
                                    <div class="w-2 h-2 bg-gray-300 rounded-full mr-1"></div>
                                    <span>+3</span>
                                </div>
                            </div>
                        </div>

                        <!-- Carte mondiale avec animation des points -->
                        <div class="h-48 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg relative overflow-hidden border border-gray-200">
                            <!-- Grille cartographique -->
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute top-0 left-1/2 w-px h-full bg-gray-300 transform -translate-x-1/2"></div>
                                <div class="absolute left-0 top-1/2 w-full h-px bg-gray-300 transform -translate-y-1/2"></div>
                                <div class="absolute top-1/4 left-0 w-full h-px bg-gray-200"></div>
                                <div class="absolute top-3/4 left-0 w-full h-px bg-gray-200"></div>
                                <div class="absolute left-1/4 top-0 bottom-0 w-px bg-gray-200"></div>
                                <div class="absolute left-3/4 top-0 bottom-0 w-px bg-gray-200"></div>
                            </div>

                            <!-- Continents réalistes -->
                            <!-- Amérique du Nord -->
                            <div class="absolute top-6 left-2">
                                <div class="relative w-12 h-10 bg-gray-300 opacity-70" style="clip-path: polygon(10% 90%, 20% 80%, 30% 85%, 40% 75%, 60% 80%, 80% 70%, 90% 50%, 95% 30%, 85% 20%, 75% 10%, 60% 15%, 40% 5%, 20% 15%, 10% 25%, 5% 40%, 0% 60%, 5% 80%);">
                                    <div class="absolute inset-0 bg-gradient-to-se from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Europe / Afrique -->
                            <div class="absolute top-16 left-1/2 transform -translate-x-1/2">
                                <div class="relative w-8 h-16 bg-gray-300 opacity-70" style="clip-path: polygon(20% 0%, 60% 5%, 80% 15%, 90% 35%, 85% 50%, 90% 65%, 80% 80%, 60% 90%, 40% 95%, 20% 90%, 10% 75%, 5% 60%, 15% 45%, 10% 30%, 20% 15%);">
                                    <div class="absolute inset-0 bg-gradient-to-s from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Asie -->
                            <div class="absolute top-8 right-4">
                                <div class="relative w-14 h-12 bg-gray-300 opacity-70" style="clip-path: polygon(0% 40%, 15% 20%, 30% 25%, 50% 15%, 70% 20%, 85% 35%, 95% 50%, 90% 70%, 75% 85%, 60% 90%, 40% 95%, 25% 85%, 10% 70%, 5% 55%);">
                                    <div class="absolute inset-0 bg-gradient-to-sw from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Océanie/Australie -->
                            <div class="absolute bottom-8 right-8">
                                <div class="relative w-6 h-4 bg-gray-300 opacity-70" style="clip-path: polygon(15% 30%, 85% 20%, 95% 60%, 80% 90%, 20% 95%, 5% 70%);">
                                    <div class="absolute inset-0 bg-gradient-to-e from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Amérique du Sud -->
                            <div class="absolute bottom-12 left-6">
                                <div class="relative w-5 h-10 bg-gray-300 opacity-70" style="clip-path: polygon(40% 0%, 70% 10%, 85% 30%, 80% 50%, 85% 70%, 75% 85%, 60% 95%, 40% 90%, 25% 85%, 20% 70%, 30% 50%, 25% 30%, 35% 15%);">
                                    <div class="absolute inset-0 bg-gradient-to-s from-gray-400 to-gray-300"></div>
                                </div>
                            </div>

                            <!-- Point central (position utilisateur) -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse shadow-lg relative">
                                    <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-75"></div>
                                    <div class="absolute inset-0.5 bg-white rounded-full"></div>
                                </div>
                            </div>

                            <!-- Points NASA (animation de visibilité) -->
                            <div class="absolute top-16 left-12 nasa-points" style="animation: nasaPointsAnimation 6s infinite;">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white text-xs font-bold">3</span>
                                </div>
                            </div>
                            <div class="absolute top-24 left-8 nasa-points" style="animation: nasaPointsAnimation 6s infinite;">
                                <div class="w-4 h-4 bg-blue-500 rounded-full shadow-md"></div>
                            </div>
                            <div class="absolute bottom-20 right-16 nasa-points" style="animation: nasaPointsAnimation 6s infinite;">
                                <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">7</span>
                                </div>
                            </div>
                            <div class="absolute top-20 right-20 w-3 h-3 bg-blue-400 rounded-full nasa-points" style="animation: nasaPointsAnimation 6s infinite;"></div>
                            <div class="absolute top-8 left-20 nasa-points" style="animation: nasaPointsAnimation 6s infinite;">
                                <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">12</span>
                                </div>
                            </div>
                            <div class="absolute bottom-8 left-16 w-3 h-3 bg-blue-400 rounded-full nasa-points" style="animation: nasaPointsAnimation 6s infinite;"></div>
                            <div class="absolute top-28 right-8 w-3 h-3 bg-blue-400 rounded-full nasa-points" style="animation: nasaPointsAnimation 6s infinite;"></div>
                            <div class="absolute bottom-32 right-24 nasa-points" style="animation: nasaPointsAnimation 6s infinite;">
                                <div class="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">5</span>
                                </div>
                            </div>

                            <!-- Points Apollo (animation de visibilité) -->
                            <div class="absolute top-12 left-16 apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white text-xs font-bold">2</span>
                                </div>
                            </div>
                            <div class="absolute bottom-16 left-12 apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">5</span>
                                </div>
                            </div>
                            <div class="absolute top-20 right-12 apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="w-4 h-4 bg-purple-500 rounded-full shadow-md"></div>
                            </div>
                            <div class="absolute bottom-24 right-20 w-3 h-3 bg-purple-400 rounded-full apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;"></div>
                            <div class="absolute top-32 left-24 apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">8</span>
                                </div>
                            </div>
                            <div class="absolute bottom-8 right-12 w-3 h-3 bg-purple-400 rounded-full apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;"></div>
                            <div class="absolute top-8 right-20 apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="w-4 h-4 bg-purple-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">3</span>
                                </div>
                            </div>
                            <div class="absolute bottom-28 left-20 w-3 h-3 bg-purple-400 rounded-full apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;"></div>
                            <div class="absolute top-36 right-16 apollo-points" style="animation: apolloPointsAnimation 6s infinite; opacity: 0;">
                                <div class="w-4 h-4 bg-purple-500 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white text-xs font-bold">4</span>
                                </div>
                            </div>
                        </div>

                        <!-- Compteur resultats avec animation -->
                        <div class="mt-4 text-center">
                            <span class="text-sm text-gray-600 results-counter" style="animation: resultsAnimation 6s infinite;">
                                <strong class="text-blue-600">47 lieux</strong> trouvés pour "NASA"
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Animations pour le mockup "Autour de moi" */
@keyframes slideRadius {
    0%, 10% {
        left: 20%; /* 200km */
    }
    45%, 55% {
        left: 80%; /* 1100km */
    }
    90%, 100% {
        left: 20%; /* 200km */
    }
}

@keyframes radiusAnimation {
    0%, 10% {
        width: 90px;
        height: 90px;
    }
    45%, 55% {
        width: 300px;
        height: 300px;
    }
    90%, 100% {
        width: 90px;
        height: 90px;
    }
}

/* Animation du texte du rayon - synchronisée avec le curseur */
.radius-display::after {
    content: '200 km';
    animation: radiusTextContent 10s infinite;
}

@keyframes radiusTextContent {
    0%, 10% {
        content: '200 km';
    }
    15% {
        content: '300 km';
    }
    20% {
        content: '400 km';
    }
    25% {
        content: '500 km';
    }
    30% {
        content: '650 km';
    }
    35% {
        content: '800 km';
    }
    40% {
        content: '950 km';
    }
    45%, 55% {
        content: '1100 km';
    }
    60% {
        content: '950 km';
    }
    65% {
        content: '800 km';
    }
    70% {
        content: '650 km';
    }
    75% {
        content: '500 km';
    }
    80% {
        content: '400 km';
    }
    85% {
        content: '300 km';
    }
    90%, 100% {
        content: '200 km';
    }
}

/* Cacher le texte original */
.radius-display {
    font-size: 0;
}

.radius-display::after {
    font-size: 0.75rem;
    font-weight: 700;
    color: #2563eb;
}

/* Animations pour le mockup "Recherche thématique" - Cycle de 6s avec pause de 7s */

/* Animation des tags avec transitions fluides */
@keyframes tagNasaAnimation {
    0%, 35% {
        background-color: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
        transform: scale(1);
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    37% {
        background-color: #e0e7ff;
        border-color: #6b7280;
        color: #6b7280;
        transform: scale(0.98);
        box-shadow: 0 1px 3px rgba(59, 130, 246, 0.2);
    }
    40% {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #9ca3af;
        transform: scale(0.96);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    43%, 85% {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #9ca3af;
        transform: scale(1);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    90% {
        background-color: #e0e7ff;
        border-color: #6b7280;
        color: #6b7280;
        transform: scale(1.02);
        box-shadow: 0 2px 3px rgba(59, 130, 246, 0.2);
    }
    93% {
        background-color: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
        transform: scale(1.03);
        box-shadow: 0 3px 6px rgba(59, 130, 246, 0.35);
    }
    100% {
        background-color: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
        transform: scale(1);
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
}

@keyframes tagApolloAnimation {
    0%, 35% {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #9ca3af;
        transform: scale(1);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    40% {
        background-color: #f3f0ff;
        border-color: #a78bfa;
        color: #8b5cf6;
        transform: scale(1.02);
        box-shadow: 0 2px 3px rgba(139, 92, 246, 0.2);
    }
    43% {
        background-color: #faf5ff;
        border-color: #8b5cf6;
        color: #7c3aed;
        transform: scale(1.03);
        box-shadow: 0 3px 6px rgba(139, 92, 246, 0.35);
    }
    50%, 85% {
        background-color: #faf5ff;
        border-color: #8b5cf6;
        color: #7c3aed;
        transform: scale(1);
        box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);
    }
    87% {
        background-color: #f3f0ff;
        border-color: #a78bfa;
        color: #8b5cf6;
        transform: scale(0.98);
        box-shadow: 0 1px 3px rgba(139, 92, 246, 0.2);
    }
    90% {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #9ca3af;
        transform: scale(0.96);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    100% {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #9ca3af;
        transform: scale(1);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
}

/* Animation des points NASA avec transition fluide */
@keyframes nasaPointsAnimation {
    0%, 35% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
    37% {
        opacity: 0.9;
        transform: scale(0.9) rotate(5deg);
    }
    39% {
        opacity: 0.6;
        transform: scale(0.7) rotate(10deg);
    }
    41% {
        opacity: 0.2;
        transform: scale(0.4) rotate(20deg);
    }
    43% {
        opacity: 0;
        transform: scale(0.2) rotate(30deg);
    }
    50%, 85% {
        opacity: 0;
        transform: scale(0.2) rotate(30deg);
    }
    87% {
        opacity: 0.2;
        transform: scale(0.4) rotate(-20deg);
    }
    89% {
        opacity: 0.6;
        transform: scale(0.7) rotate(-10deg);
    }
    91% {
        opacity: 0.9;
        transform: scale(0.9) rotate(-5deg);
    }
    93% {
        opacity: 1;
        transform: scale(1.05) rotate(0deg);
    }
    95% {
        opacity: 1;
        transform: scale(1.02);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Animation des points Apollo avec transition fluide */
@keyframes apolloPointsAnimation {
    0%, 35% {
        opacity: 0;
        transform: scale(0.2) rotate(-30deg);
    }
    37% {
        opacity: 0.2;
        transform: scale(0.4) rotate(-20deg);
    }
    39% {
        opacity: 0.6;
        transform: scale(0.7) rotate(-10deg);
    }
    41% {
        opacity: 0.9;
        transform: scale(0.9) rotate(-5deg);
    }
    43% {
        opacity: 1;
        transform: scale(1.05) rotate(0deg);
    }
    45% {
        opacity: 1;
        transform: scale(1.02);
    }
    50%, 85% {
        opacity: 1;
        transform: scale(1);
    }
    87% {
        opacity: 0.9;
        transform: scale(0.9) rotate(5deg);
    }
    89% {
        opacity: 0.6;
        transform: scale(0.7) rotate(10deg);
    }
    91% {
        opacity: 0.2;
        transform: scale(0.4) rotate(20deg);
    }
    93% {
        opacity: 0;
        transform: scale(0.2) rotate(30deg);
    }
    100% {
        opacity: 0;
        transform: scale(0.2) rotate(30deg);
    }
}

/* Animation du compteur de résultats avec transition marquée */
.results-counter::after {
    content: '52 lieux trouvés pour "NASA"';
    animation: resultsContentAnimation 6s infinite;
}

.results-counter {
    font-size: 0;
    animation: resultsContainerAnimation 6s infinite;
}

.results-counter::after {
    font-size: 0.875rem;
    color: #6b7280;
}

@keyframes resultsContainerAnimation {
    0%, 35.7% {
        transform: scale(1);
        opacity: 1;
    }
    40% {
        transform: scale(0.9);
        opacity: 0.7;
    }
    42.8% {
        transform: scale(1.05);
        opacity: 1;
    }
    50%, 85.7% {
        transform: scale(1);
        opacity: 1;
    }
    90% {
        transform: scale(0.9);
        opacity: 0.7;
    }
    92.8% {
        transform: scale(1.05);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes resultsContentAnimation {
    0%, 35% {
        content: '52 lieux trouvés pour "NASA"';
    }
    43%, 85% {
        content: '31 lieux trouvés pour "Apollo"';
    }
    100% {
        content: '52 lieux trouvés pour "NASA"';
    }
}

/* Styles pour les éléments strong dans le compteur */
.results-counter::after {
    background: linear-gradient(to right, #2563eb 0%, #2563eb 100%);
    background-clip: text;
    -webkit-background-clip: text;
}

/* Animation spécifique pour les couleurs du compteur */
@keyframes resultsContentAnimation {
    0%, 35.7% {
        content: '47 lieux trouvés pour "NASA"';
    }
    50%, 85.7% {
        content: '23 lieux trouvés pour "Apollo"';
    }
    100% {
        content: '47 lieux trouvés pour "NASA"';
    }
}
</style>

