@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('locales.supported');
    $currentRoute = request()->route()?->getName();
    $routeParams = request()->route()?->parameters() ?? [];
@endphp

<div class="relative inline-block text-left language-switcher">
    {{-- Bouton actuel - Responsive: plus petit sur mobile, adaptatif sur desktop --}}
    <button class="lang-switcher-button inline-flex items-center px-2 lg:px-3 py-1.5 lg:py-2 border border-gray-300 shadow-sm text-xs lg:text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
        <span class="lang-switcher-text">{{ strtoupper($currentLocale) }}</span>

        {{-- Spinner de chargement (caché par défaut) --}}
        <svg class="lang-switcher-spinner hidden ml-1 lg:ml-2 h-3 w-3 lg:h-4 lg:w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        {{-- Chevron (visible par défaut) --}}
        <svg class="lang-switcher-chevron ml-1 lg:ml-2 h-3 w-3 lg:h-4 lg:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    {{-- Menu déroulant - Responsive: position adaptative selon la taille écran --}}
    <div class="lang-switcher-menu origin-top-right absolute right-0 lg:right-0 mt-2 w-28 lg:w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden">

        <div class="py-1">
            @foreach($supportedLocales as $locale)

                @if($locale !== $currentLocale)

                    <a href="{{ switchLocalRoute($locale) }}" class="lang-switcher-link w-full text-left px-3 lg:px-4 py-1.5 lg:py-2 text-xs lg:text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center transition-colors" data-locale="{{ $locale }}">
                        {{ strtoupper($locale) }}
                        <span class="ml-1 lg:ml-2 text-gray-500 text-xs lg:text-sm">
                            @if($locale === 'fr')
                                Français
                            @elseif($locale === 'en')
                                English
                            @endif
                        </span>
                    </a>

                @endif

            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const switcher = document.querySelector('.language-switcher');
    const button = switcher?.querySelector('.lang-switcher-button');
    const menu = switcher?.querySelector('.lang-switcher-menu');
    const spinner = switcher?.querySelector('.lang-switcher-spinner');
    const chevron = switcher?.querySelector('.lang-switcher-chevron');
    const text = switcher?.querySelector('.lang-switcher-text');
    const links = switcher?.querySelectorAll('.lang-switcher-link');

    if (!switcher || !button || !menu) {
        console.error('Language switcher elements not found');
        return;
    }

    // Toggle menu
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });

    // Gestion du changement de langue avec spinner
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            // Afficher le spinner et masquer le chevron
            if (spinner && chevron && text) {
                spinner.classList.remove('hidden');
                chevron.classList.add('hidden');
                text.textContent = text.textContent; // Garder le texte actuel

                // Désactiver le bouton pour éviter les clics multiples
                button.disabled = true;
                button.classList.add('opacity-75', 'cursor-not-allowed');
            }

            // Fermer le menu
            menu.classList.add('hidden');

            // La navigation se fera automatiquement via le href
        });
    });

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', function(event) {
        if (!switcher.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // Fermer le menu sur redimensionnement d'écran
    window.addEventListener('resize', function() {
        menu.classList.add('hidden');
    });

    // Fermer le menu avec la touche Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            menu.classList.add('hidden');
        }
    });
});
</script>
