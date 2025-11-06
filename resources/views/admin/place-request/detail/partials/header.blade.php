@props(['placeRequest'])

<div class="bg-white rounded-md sticky top-[3.5rem] sm:top-16 z-40 max-w-7xl mx-auto shadow-md">
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
                    <a href="{{ route('admin.place-requests.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        Propositions de lieux
                    </a>
                </li>
                <li class="flex items-center min-w-0">
                    <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-400 mx-2 flex-shrink-0" />
                    <span class="text-gray-900 font-medium truncate">
                        {{ $placeRequest->title }}
                    </span>
                </li>
            </ol>
        </nav>

        {{-- Header content --}}
        <div class="py-4 sm:py-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                {{-- Titre et métadonnées --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                            {{ $placeRequest->title }}
                        </h1>
                        <x-admin.badge-status :status="$placeRequest->status" />
                    </div>
                    <p class="mt-3 text-xs sm:text-sm text-gray-500">
                        ID: {{ $placeRequest->id }} •
                        Proposé le {{ $placeRequest->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>

                {{-- Actions - Style moderne 2025 --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.place-requests.index') }}"
                       class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                        <x-heroicon-o-arrow-left class="h-4 w-4 sm:mr-2" />
                        <span class="hidden sm:inline">Retour</span>
                    </a>

                    @if($placeRequest->status->canBeModerated())
                        <a href="{{ route('admin.places.create', ['request_id' => $placeRequest->id]) }}"
                           class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 hover:text-green-800 hover:border-green-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition-all duration-200">
                            <x-heroicon-o-check class="h-4 w-4 sm:mr-2" />
                            <span class="hidden sm:inline">Accepter</span>
                        </a>
                    @endif

                    @if($placeRequest->status->canBeRefused())
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
