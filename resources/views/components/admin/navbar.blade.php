@php
    $user = auth()->user();
@endphp

<!-- Navbar admin responsive -->
<nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-[90em] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">

            <!-- Logo + Titre Administration -->
            <div class="flex items-center flex-shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-1.5 sm:space-x-2">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-indigo-600 rounded-md sm:rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L13.09 8.26L22 9L13.09 9.74L12 16L10.91 9.74L2 9L10.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg sm:text-xl font-bold text-gray-900">Administration</span>
                        <span class="text-[10px] sm:text-xs text-gray-500 -mt-1 hidden sm:block">Explo.space</span>
                    </div>
                </a>
            </div>

            <!-- Navigation principale (desktop) + Actions -->
            <div class="hidden lg:flex lg:items-center lg:space-x-6 grow-1 justify-center px-4" id="nav-container">

                <!-- Links navigation -->
                <nav class="flex items-center ml-auto space-x-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-base font-medium transition-colors rounded-md hover:bg-indigo-50 @if(request()->routeIs('admin.dashboard')) text-indigo-600 bg-indigo-50 @endif">
                        Tableau de bord
                    </a>
                    <a href="{{ route('admin.places.index') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-base font-medium transition-colors rounded-md hover:bg-indigo-50 @if(request()->routeIs('admin.places.*')) text-indigo-600 bg-indigo-50 @endif">
                        Lieux
                    </a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-base font-medium transition-colors rounded-md hover:bg-indigo-50">
                        Tags
                    </a>
                    <a href="#" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-base font-medium transition-colors rounded-md hover:bg-indigo-50">
                        Catégories
                    </a>

                    {{-- Dropdown Demandes --}}
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" type="button" class="flex items-center space-x-1 text-gray-700 hover:text-indigo-600 px-3 py-2 text-base font-medium transition-colors rounded-md hover:bg-indigo-50 @if(request()->routeIs('admin.place-requests.*') || request()->routeIs('admin.edit-requests.*')) text-indigo-600 bg-indigo-50 @endif">
                            <span>Demandes</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div :class="{ 'hidden': !open }"
                             class="hidden absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 border border-gray-200 z-50">
                            <a href="{{ route('admin.place-requests.index') }}" class="block px-4 py-2 text-sm transition-colors @if(request()->routeIs('admin.place-requests.*')) text-indigo-600 bg-indigo-50 font-medium @else text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 @endif">
                                Propositions de lieux
                            </a>
                            <a href="{{ route('admin.edit-requests.index') }}" class="block px-4 py-2 text-sm transition-colors @if(request()->routeIs('admin.edit-requests.*')) text-indigo-600 bg-indigo-50 font-medium @else text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 @endif">
                                Modifications/Signalements
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Dropdown utilisateur -->
                <div class="relative ml-auto" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" type="button" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md hover:bg-indigo-50 transition-colors">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-indigo-600">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <span class="font-medium">{{ $user->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <div :class="{ 'hidden': !open }"
                         class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-200 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                        </div>

                        @if($user->isSuperAdmin())
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                Paramètres
                            </a>
                        @endif

                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Burger menu (mobile) -->
            <button type="button" class="lg:hidden text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 p-2 rounded-md transition-colors" id="mobile-menu-button">
                <svg class="w-7 h-7 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-open-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg class="w-7 h-7 sm:w-6 sm:h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menu-close-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Menu mobile (caché par défaut) -->
        <div class="lg:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('admin.dashboard')) text-indigo-600 bg-indigo-50 @endif">
                    Tableau de bord
                </a>
                <a href="#" class="block text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2 rounded-md text-base font-medium">
                    Lieux
                </a>
                <a href="#" class="block text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2 rounded-md text-base font-medium">
                    Tags
                </a>
                <a href="#" class="block text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2 rounded-md text-base font-medium">
                    Catégories
                </a>

                {{-- Dropdown Demandes Mobile --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="w-full flex items-center justify-between text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 px-3 py-2 rounded-md text-base font-medium @if(request()->routeIs('admin.place-requests.*') || request()->routeIs('admin.edit-requests.*')) text-indigo-600 bg-indigo-50 @endif">
                        <span>Demandes</span>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="pl-6 space-y-1 mt-1">
                        <a href="{{ route('admin.place-requests.index') }}" class="block px-3 py-2 rounded-md text-sm transition-colors @if(request()->routeIs('admin.place-requests.*')) text-indigo-600 bg-indigo-50 font-medium @else text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 @endif">
                            Propositions de lieux
                        </a>
                        <a href="{{ route('admin.edit-requests.index') }}" class="block px-3 py-2 rounded-md text-sm transition-colors @if(request()->routeIs('admin.edit-requests.*')) text-indigo-600 bg-indigo-50 font-medium @else text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 @endif">
                            Modifications/Signalements
                        </a>
                    </div>
                </div>

                <!-- User info mobile -->
                <div class="pt-4 border-t border-gray-200 mt-4">
                    <div class="px-3 py-2 mb-2">
                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                    </div>

                    @if($user->isSuperAdmin())
                        <a href="#" class="block px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition-colors">
                            Paramètres
                        </a>
                    @endif

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Script toggle mobile menu -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');
        const openIcon = document.getElementById('menu-open-icon');
        const closeIcon = document.getElementById('menu-close-icon');

        if (button && menu) {
            button.addEventListener('click', function() {
                menu.classList.toggle('hidden');
                openIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        }
    });
</script>
