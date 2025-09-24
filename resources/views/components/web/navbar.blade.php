<!-- Navbar responsive moderne -->
<nav class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-[90em] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">
            <!-- Logo - Responsive -->
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center space-x-1.5 sm:space-x-2">
                    <!-- Logo COSMAP - Responsive size -->
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-600 rounded-md sm:rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                    <span class="text-lg sm:text-xl font-bold text-gray-900">COSMAP</span>
                </a>
            </div>

            <!-- Navigation principale - Desktop uniquement -->
            <div class="hidden lg:flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-2 xl:px-3 py-2 text-md font-medium transition-colors rounded-md hover:bg-blue-50">
                    Accueil
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 px-2 xl:px-3 py-2 text-md font-medium transition-colors rounded-md hover:bg-blue-50">
                    Fonctionnalités
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 px-2 xl:px-3 py-2 text-md font-medium transition-colors rounded-md hover:bg-blue-50">
                    Explorer
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 px-2 xl:px-3 py-2 text-md font-medium transition-colors rounded-md hover:bg-blue-50">
                    Communauté
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 px-2 xl:px-3 py-2 text-md font-medium transition-colors rounded-md hover:bg-blue-50">
                    À propos
                </a>
            </div>

            <!-- Actions - Desktop et Tablette -->
            <div class="hidden lg:flex items-center space-x-2 sm:space-x-">
                <a href="#" class="bg-blue-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md sm:rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Proposer un lieu
                </a>
                <a href="#" class="text-gray-700 hover:text-blue-600 px-2 sm:px-3 py-2 text-sm font-medium transition-colors rounded-md hover:bg-blue-50">
                    Admin
                </a>
            </div>

            <!-- Menu mobile/tablet button -->
            <div class="lg:hidden">
                <button type="button" class="text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-md transition-colors" id="mobile-menu-button">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-open-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-close-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu mobile/tablette - Improved -->
    <div class="lg:hidden hidden" id="mobile-menu">
        <div class="px-4 pt-3 pb-4 space-y-1 bg-white border-t border-gray-100 shadow-sm">
            <!-- Navigation links -->
            <div class="space-y-1">
                <a href="{{ route('home') }}" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                    Accueil
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                    Fonctionnalités
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                    Explorer
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                    Communauté
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2.5 text-sm font-medium rounded-md transition-colors">
                    À propos
                </a>
            </div>

            <!-- Actions mobile -->
            <div class="pt-4 space-y-2 border-t border-gray-100 max-w-[30em] flex flex-col items-center mx-auto">
                <a href="#" class="block w-full bg-blue-600 text-white hover:bg-blue-700 px-3 py-2.5 rounded-md text-sm font-medium text-center transition-colors">
                    Proposer un lieu
                </a>
                <a href="#" class="block w-full bg-white text-slate-600 border px-3 py-2.5 rounded-md text-sm font-medium text-center transition-colors">
                    Admin
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Script pour le menu mobile amélioré -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOpenIcon = document.getElementById('menu-open-icon');
    const menuCloseIcon = document.getElementById('menu-close-icon');

    menuButton.addEventListener('click', function() {
        const isMenuOpen = !mobileMenu.classList.contains('hidden');

        if (isMenuOpen) {
            // Fermer le menu
            mobileMenu.classList.add('hidden');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
        } else {
            // Ouvrir le menu
            mobileMenu.classList.remove('hidden');
            menuOpenIcon.classList.add('hidden');
            menuCloseIcon.classList.remove('hidden');
        }
    });

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', function(event) {
        const isClickInsideNav = event.target.closest('nav');
        if (!isClickInsideNav && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
        }
    });

    // Fermer le menu sur redimensionnement d'écran
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            mobileMenu.classList.add('hidden');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
        }
    });
});
</script>
