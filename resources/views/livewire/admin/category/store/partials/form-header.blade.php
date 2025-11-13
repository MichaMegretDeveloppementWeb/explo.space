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
    <div class="max-w-7xl mx-auto" x-ref="headerContent">
        {{-- Breadcrumb --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <x-heroicon-o-home class="w-4 h-4 mr-2" />
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                        <a href="{{ route('admin.categories.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                            Catégories
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                            {{ $mode === 'create' ? 'Nouvelle catégorie' : 'Éditer' }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-5">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    {{-- Titre et métadonnées --}}
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-gray-900">
                            @if($mode === 'create')
                                Créer une nouvelle catégorie
                            @else
                                Modifier la catégorie
                                @if(!empty($original_name))
                                    <span class="text-gray-600">: {{ $original_name }}</span>
                                @endif
                            @endif
                        </h1>

                        @if($mode === 'edit' && $category)
                            <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <x-heroicon-o-calendar class="w-4 h-4 mr-1.5" />
                                    <span>Créé le {{ $category->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($category->updated_at && !$category->created_at->eq($category->updated_at))
                                    <div class="flex items-center">
                                        <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                                        <span>Modifié le {{ $category->updated_at->format('d/m/Y à H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.categories.index') }}"
                           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                            <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Annuler</span>
                        </a>

                        @if($mode === 'edit')
                            <button type="button"
                                    wire:click="confirmDeleteModal"
                                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 hover:text-red-800 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                                <x-heroicon-o-trash class="w-5 h-5 sm:mr-2" />
                                <span class="hidden sm:inline">Supprimer</span>
                            </button>
                        @endif
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
                <a href="{{ route('admin.categories.index') }}"
                   class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                    <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                    <span class="hidden sm:inline">Annuler</span>
                </a>

                @if($mode === 'edit')
                    <button type="button"
                            wire:click="confirmDeleteModal"
                            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 hover:text-red-800 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                        <x-heroicon-o-trash class="w-5 h-5 sm:mr-2" />
                        <span class="hidden sm:inline">Supprimer</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
