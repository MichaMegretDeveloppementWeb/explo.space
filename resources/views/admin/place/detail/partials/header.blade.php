@props(['place'])

{{-- Alpine.js avec Intersection Observer pour détecter quand le header sort de la vue --}}
<div x-data="{
    headerOutOfView: false,
    navbarHeight: 0,
    init() {
        // Calculer dynamiquement la hauteur de la navbar
        const navbar = document.querySelector('nav.sticky.top-0');
        if (navbar) {
            this.navbarHeight = navbar.getBoundingClientRect().height;
        }

        // Créer un Intersection Observer pour détecter quand le header sort de la vue
        const observer = new IntersectionObserver(
            ([entry]) => {
                // headerOutOfView = true quand le header n'est plus visible
                this.headerOutOfView = !entry.isIntersecting;
            },
            {
                // Prendre en compte la navbar sticky (hauteur calculée dynamiquement)
                rootMargin: `-${this.navbarHeight}px 0px 0px 0px`,
                threshold: 0
            }
        );

        // Observer le header
        const headerElement = this.$refs.headerContent;
        if (headerElement) {
            observer.observe(headerElement);
        }

        // Recalculer la hauteur de la navbar si la fenêtre est redimensionnée
        window.addEventListener('resize', () => {
            if (navbar) {
                this.navbarHeight = navbar.getBoundingClientRect().height;
            }
        });
    }
}">

    {{-- Header complet (reste toujours dans le flux) --}}
    <div class="max-w-7xl mx-auto bg-white rounded-md shadow-md" x-ref="headerContent">
        <div class="px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex py-3 text-sm overflow-x-auto" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 whitespace-nowrap">
                    <li class="flex-shrink-0">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            <x-heroicon-o-home class="h-4 w-4" />
                        </a>
                    </li>
                    <li class="flex items-center flex-shrink-0">
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400 mx-2" />
                        <a href="{{ route('admin.places.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            Lieux
                        </a>
                    </li>
                    <li class="flex items-center min-w-0">
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400 mx-2 flex-shrink-0" />
                        <span class="text-gray-900 font-medium truncate">
                            {{ $place->translations->where('locale', 'fr')->first()?->title ?? "Lieu #{$place->id}" }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Titre et métadonnées --}}
            <div class="py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    {{-- Titre et métadonnées --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                                {{ $place->translations->where('locale', 'fr')->first()?->title ?? "Lieu #{$place->id}" }}
                            </h1>
                            @if($place->is_featured)
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-300 w-fit">
                                    <x-heroicon-s-star class="h-4 w-4 mr-1" />
                                    À l'affiche
                                </span>
                            @endif
                        </div>
                        <p class="mt-3 text-xs sm:text-sm text-gray-500">
                            ID: {{ $place->id }} •
                            Créé le {{ $place->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    {{-- Actions dans le header original --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.places.index') }}"
                           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200" />
                            <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Retour</span>
                        </a>

                        <a href="{{ route('admin.places.edit', ['id' => $place->id]) }}"
                           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 hover:text-green-800 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200">
                            <x-heroicon-o-pencil class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Modifier</span>
                        </a>

                        <button type="button"
                                onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce lieu ? Cette action est irréversible.')) { document.getElementById('delete-form-{{ $place->id }}').submit(); }"
                                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 hover:text-red-800 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                            <x-heroicon-o-trash class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Supprimer</span>
                        </button>

                        <form id="delete-form-{{ $place->id }}"
                              action="{{ route('admin.places.destroy', ['id' => $place->id]) }}"
                              method="POST"
                              class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Barre d'actions sticky (apparaît seulement quand le header est hors de vue) --}}
    <div x-show="headerOutOfView"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         :style="`top: ${navbarHeight}px`"
         class="fixed left-0 right-0 bg-white shadow-md z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.places.index') }}"
                   class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200" />
                    <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                    <span class="hidden sm:inline">Retour</span>
                </a>

                <a href="{{ route('admin.places.edit', ['id' => $place->id]) }}"
                   class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 hover:text-green-800 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200">
                    <x-heroicon-o-pencil class="h-4 w-4 sm:mr-2" />
                    <span class="hidden sm:inline">Modifier</span>
                </a>

                <button type="button"
                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce lieu ? Cette action est irréversible.')) { document.getElementById('delete-form-{{ $place->id }}').submit(); }"
                        class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 hover:text-red-800 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                    <x-heroicon-o-trash class="h-4 w-4 sm:mr-2" />
                    <span class="hidden sm:inline">Supprimer</span>
                </button>
            </div>
        </div>
    </div>
</div>
