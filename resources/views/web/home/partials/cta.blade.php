<!-- Section CTA moderne avec fond cosmos -->
<section class="relative py-16 sm:py-20 md:py-24 lg:py-32 overflow-hidden">
    <!-- Image de fond : Nebuleuse coloree avec etoiles scintillantes, 1920x600px -->
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900"></div>
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Particules flottantes adaptatives -->
    <div class="absolute inset-0">
        <!-- Mobile : particules plus simples et moins nombreuses -->
        <div class="absolute top-6 sm:top-12 left-6 sm:left-12 w-2 sm:w-3 h-2 sm:h-3 bg-purple-400 rounded-full animate-pulse opacity-60"></div>
        <div class="absolute top-16 sm:top-32 right-12 sm:right-24 w-1.5 sm:w-2 h-1.5 sm:h-2 bg-pink-400 rounded-full animate-pulse delay-500 opacity-40"></div>
        <div class="absolute bottom-12 sm:bottom-24 left-16 sm:left-32 w-1 h-1 bg-blue-300 rounded-full animate-pulse delay-1000"></div>
        <div class="absolute bottom-8 sm:bottom-16 right-8 sm:right-16 w-1.5 sm:w-2 h-1.5 sm:h-2 bg-indigo-400 rounded-full animate-pulse delay-700"></div>
        
        <!-- Particules supplémentaires pour tablette et desktop -->
        <div class="hidden sm:block absolute top-20 left-1/4 w-1 h-1 bg-cyan-300 rounded-full animate-pulse delay-300 opacity-50"></div>
        <div class="hidden lg:block absolute bottom-32 right-1/3 w-1.5 h-1.5 bg-violet-400 rounded-full animate-pulse delay-900 opacity-30"></div>
    </div>

    <div class="relative max-w-6xl mx-auto text-center px-4 sm:px-6 lg:px-8">

        <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 sm:mb-8 leading-tight px-2 sm:px-0">
            <span class="block sm:inline">Votre voyage spatial</span>
            <span class="bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 bg-clip-text text-transparent block sm:inline">
                commence ici
            </span>
        </h2>

        <p class="text-base sm:text-lg md:text-xl text-gray-200 mb-8 sm:mb-10 md:mb-12 max-w-2xl sm:max-w-3xl mx-auto leading-relaxed px-2 sm:px-0">
            <span class="block sm:inline">Explorez des milliers de lieux emblématiques, depuis les rampes de lancement historiques</span>
            <span class="block sm:inline">jusqu'aux observatoires de pointe. L'univers vous attend.</span>
        </p>

        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center items-center">
            <a href="#" class="group relative bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 sm:px-8 md:px-10 py-3.5 sm:py-4 md:py-5 rounded-xl sm:rounded-2xl text-base sm:text-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl sm:shadow-2xl shadow-purple-900/50 w-full sm:w-auto max-w-sm sm:max-w-none">
                <span class="relative z-10 flex items-center justify-center">
                    <span class="truncate">Démarrer l'exploration</span>
                    <x-heroicon-o-arrow-right class="w-4 h-4 sm:w-5 sm:h-5 ml-2 group-hover:translate-x-1 transition-transform flex-shrink-0" />
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-pink-400 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
            </a>
            
            <!-- Bouton secondaire optionnel pour mobile -->
            <a href="#" class="text-white/90 hover:text-white text-sm sm:text-base font-medium border-b border-white/30 hover:border-white/60 transition-colors pb-1">
                Proposer un lieu
            </a>
        </div>
    </div>
</section>
