<!-- Navbar responsive moderne -->
<nav class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-[90em] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">

            <!-- Logo -->
            <div class="flex items-center flex-shrink-0">
                <a href="{{ localRoute('home') }}" class="flex items-center space-x-1.5 sm:space-x-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-600 rounded-md sm:rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                    <span class="text-lg sm:text-xl font-bold text-gray-900">{{config('app.name')}}</span>
                </a>
            </div>

            <!-- Container Navigation + Actions (responsive: horizontal desktop, vertical mobile) -->
            <div class="hidden lg:flex lg:items-center lg:flex-row absolute lg:relative top-full lg:top-auto left-0 lg:left-auto right-0 lg:right-auto bg-white lg:bg-transparent border-t lg:border-t-0 border-gray-100 lg:shadow-none shadow-sm flex-col space-y-4 lg:space-y-0 p-4 lg:p-0 grow-1 justify-between"
                 id="nav-container">

                <!-- Navigation Links -->
                <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-6 grow-1 justify-center lg:mx-4">
                    @foreach ($navigationLinks as $link)
                        <a href="{{ $link['url'] }}" class="px-3 lg:px-2 py-2.5 lg:py-2 text-md lg:text-base font-medium transition-colors rounded-md @if($link['active']) text-blue-600 bg-blue-200 @else text-gray-700 hover:text-blue-600 hover:bg-blue-50 @endif">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-3 pt-4 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                    <a href="{{ $primaryAction['url'] }}" class="bg-blue-600 text-white px-4 py-2.5 lg:py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors text-center">
                        {{ $primaryAction['label'] }}
                    </a>
                    <div class="flex justify-center lg:block">
                        <x-web.language-switcher />
                    </div>
                </div>
            </div>

            <!-- Burger Button (visible only on mobile) -->
            <button type="button" class="lg:hidden text-gray-700 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-md transition-colors" id="mobile-menu-button">
                <svg class="w-7 h-7 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-open-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg class="w-7 h-7 sm:w-6 sm:h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-close-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>

    </div>
</nav>

<!-- Script pour le menu mobile responsive -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('mobile-menu-button');
    const navContainer = document.getElementById('nav-container');
    const menuOpenIcon = document.getElementById('menu-open-icon');
    const menuCloseIcon = document.getElementById('menu-close-icon');

    menuButton.addEventListener('click', function() {
        const isMenuOpen = !navContainer.classList.contains('hidden');

        if (isMenuOpen) {
            // Fermer le menu
            navContainer.classList.add('hidden');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
        } else {
            // Ouvrir le menu
            navContainer.classList.remove('hidden');
            menuOpenIcon.classList.add('hidden');
            menuCloseIcon.classList.remove('hidden');
        }
    });

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', function(event) {
        const isClickInsideNav = event.target.closest('nav');
        if (!isClickInsideNav && !navContainer.classList.contains('hidden')) {
            navContainer.classList.add('hidden');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
        }
    });

    // Fermer le menu sur redimensionnement d'écran
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) { // lg breakpoint
            navContainer.classList.add('hidden');
            menuOpenIcon.classList.remove('hidden');
            menuCloseIcon.classList.add('hidden');
        }
    });
});
</script>
