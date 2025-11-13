@props(['editRequest'])

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
                        <a href="{{ route('admin.edit-requests.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                            Modifications & Signalements
                        </a>
                    </li>
                    <li class="flex items-center min-w-0">
                        <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400 mx-2 flex-shrink-0" />
                        <span class="text-gray-900 font-medium truncate">
                            {{ $editRequest->getTypeLabel() }} - {{ $editRequest->place->translate('fr')->title }}
                        </span>
                    </li>
                </ol>
            </nav>

            {{-- Titre et métadonnées --}}
            <div class="py-4 sm:py-6">
                <div class="flex flex-col lg:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                                {{ $editRequest->getTypeLabel() }}
                            </h1>
                            <x-admin.badge-status :status="$editRequest->status" />
                        </div>
                        <p class="mt-1 text-base text-gray-700 font-medium">
                            Lieu : {{ $editRequest->place->translate('fr')->title }}
                        </p>
                        <p class="mt-3 text-xs sm:text-sm text-gray-500">
                            ID: {{ $editRequest->id }} •
                            Soumis le {{ $editRequest->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    {{-- Actions dans le header original --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Bouton Retour --}}
                    <a href="{{ route('admin.edit-requests.index') }}"
                       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                        <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                        <span class="hidden sm:inline">Retour</span>
                    </a>

                    @if($editRequest->status->canBeModerated())
                        {{-- Bouton Accepter/Appliquer selon le type --}}
                        @if($editRequest->isSignalement())
                            {{-- Signalement : Marquer comme traité --}}
                            <button type="button"
                                    wire:click="acceptSignalement"
                                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 hover:text-green-800 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                                <x-heroicon-o-check class="h-4 w-4 sm:mr-2" />
                                <span class="hidden sm:inline">Marquer comme traité</span>
                                <span class="sm:hidden">Traité</span>
                            </button>
                        @endif

                        @if($editRequest->isModification())
                            {{-- Modification : Appliquer les modifications sélectionnées --}}
                            <button type="button"
                                    wire:click="applyModification"
                                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 hover:text-blue-800 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                                <x-heroicon-o-pencil class="h-4 w-4 sm:mr-2" />
                                <span class="hidden sm:inline">Appliquer les modifications</span>
                                <span class="sm:hidden">Appliquer</span>
                            </button>
                        @endif

                        @if($editRequest->isPhotoSuggestion())
                            {{-- Photo Suggestion : Appliquer les photos sélectionnées --}}
                            <button type="button"
                                    wire:click="applyPhotoSuggestion"
                                    class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-purple-700 bg-white hover:bg-purple-50 hover:text-purple-800 hover:border-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                                <x-heroicon-o-photo class="h-4 w-4 sm:mr-2" />
                                <span class="hidden sm:inline">Appliquer les photos</span>
                                <span class="sm:hidden">Appliquer</span>
                            </button>
                        @endif
                    @endif

                    @if($editRequest->status->canBeRefused())
                        {{-- Bouton Refuser --}}
                        <button type="button"
                                wire:click="openRefusalModal"
                                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 hover:text-red-800 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                            <x-heroicon-o-x-mark class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Refuser</span>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1">
            <div class="flex items-center justify-end gap-2">
                {{-- Bouton Retour --}}
                <a href="{{ route('admin.edit-requests.index') }}"
                   class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                    <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                    <span class="hidden sm:inline">Retour</span>
                </a>

                @if($editRequest->status->canBeModerated())
                    {{-- Bouton Accepter/Appliquer selon le type --}}
                    @if($editRequest->isSignalement())
                        {{-- Signalement : Marquer comme traité --}}
                        <button type="button"
                                wire:click="acceptSignalement"
                                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 hover:text-green-800 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                            <x-heroicon-o-check class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Marquer comme traité</span>
                            <span class="sm:hidden">Traité</span>
                        </button>
                    @endif

                    @if($editRequest->isModification())
                        {{-- Modification : Appliquer les modifications sélectionnées --}}
                        <button type="button"
                                wire:click="applyModification"
                                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 hover:text-blue-800 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                            <x-heroicon-o-pencil class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Appliquer les modifications</span>
                            <span class="sm:hidden">Appliquer</span>
                        </button>
                    @endif

                    @if($editRequest->isPhotoSuggestion())
                        {{-- Photo Suggestion : Appliquer les photos sélectionnées --}}
                        <button type="button"
                                wire:click="applyPhotoSuggestion"
                                class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-purple-700 bg-white hover:bg-purple-50 hover:text-purple-800 hover:border-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                            <x-heroicon-o-photo class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Appliquer les photos</span>
                            <span class="sm:hidden">Appliquer</span>
                        </button>
                    @endif
                @endif

                @if($editRequest->status->canBeRefused())
                    {{-- Bouton Refuser --}}
                    <button type="button"
                            wire:click="openRefusalModal"
                            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 hover:text-red-800 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200 cursor-pointer">
                        <x-heroicon-o-x-mark class="h-4 w-4 sm:mr-2" />
                        <span class="hidden sm:inline">Refuser</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
