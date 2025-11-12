@push('head')
<style>
    /* Cacher la scrollbar sur mobile pour les pills */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;  /* Chrome, Safari, Opera */
    }
</style>
@endpush

<div>
    {{-- Mobile: Horizontal Scroll Pills avec Navigation (visible only on mobile) --}}
    <div class="sm:hidden mb-6" x-data="{
        showLeftArrow: false,
        showRightArrow: true,
        updateArrows() {
            const container = this.$refs.scrollContainer;
            const maxScroll = container.scrollWidth - container.clientWidth;
            this.showLeftArrow = container.scrollLeft > 10;
            this.showRightArrow = container.scrollLeft < (maxScroll - 10);
        },
        scrollLeft() {
            const container = this.$refs.scrollContainer;
            container.scrollBy({ left: -200, behavior: 'smooth' });
            setTimeout(() => this.updateArrows(), 100);
        },
        scrollRight() {
            const container = this.$refs.scrollContainer;
            container.scrollBy({ left: 200, behavior: 'smooth' });
            setTimeout(() => this.updateArrows(), 100);
        },
        scrollToTab(event) {
            const button = event.currentTarget;
            button.scrollIntoView({
                behavior: 'smooth',
                inline: 'center',
                block: 'nearest'
            });
            setTimeout(() => this.updateArrows(), 300);
        }
    }" x-init="
        $nextTick(() => {
            updateArrows();
            $refs.scrollContainer.addEventListener('scroll', () => updateArrows());
        });
    ">
        <div class="relative">
            {{-- Flèche gauche avec gradient --}}
            <div
                x-show="showLeftArrow"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute left-0 top-0 bottom-0 z-10 flex items-center pointer-events-none p-2"
                style="background: linear-gradient(to right, rgba(249, 250, 251, 1) 20%, rgba(249, 250, 251, 0) 100%); aspect-ratio: 1/1"
            >
                <button
                    @click="scrollLeft()"
                    class="pointer-events-auto ml-1 w-7 h-7 bg-white rounded-full shadow-md border border-gray-200 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Container scrollable --}}
            <div
                x-ref="scrollContainer"
                class="overflow-x-auto overflow-y-hidden scrollbar-hide"
            >
                <nav class="flex space-x-2 px-1 pr-4" style="min-width: min-content;">
                    {{-- Profil Button --}}
                    <button
                        wire:click="setActiveTab('profile')"
                        @click="scrollToTab($event)"
                        class="flex-shrink-0 px-4 py-2.5 rounded-lg border border-gray-200 text-sm transition-all duration-200 whitespace-nowrap
                               {{ $activeTab === 'profile' ? 'bg-indigo-100 text-indigo-700 font-medium' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                    >
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Mon profil</span>
                        </div>
                    </button>

                    {{-- Mot de passe Button --}}
                    <button
                        wire:click="setActiveTab('password')"
                        @click="scrollToTab($event)"
                        class="flex-shrink-0 px-4 py-2.5 rounded-lg border border-gray-200 text-sm transition-all duration-200 whitespace-nowrap
                               {{ $activeTab === 'password' ? 'bg-indigo-100 text-indigo-700 font-medium' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                    >
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span>Mot de passe</span>
                        </div>
                    </button>

                    {{-- Créer admin Button (Super-admin only) --}}
                    @can('createUser', App\Models\User::class)
                        <button
                            wire:click="setActiveTab('create-admin')"
                            @click="scrollToTab($event)"
                            class="flex-shrink-0 px-4 py-2.5 rounded-lg border border-gray-200 text-sm transition-all duration-200 whitespace-nowrap
                                   {{ $activeTab === 'create-admin' ? 'bg-indigo-100 text-indigo-700 font-medium' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <span>Créer un administrateur</span>
                            </div>
                        </button>

                        {{-- Liste admin Button (Super-admin only) --}}
                        <button
                            wire:click="setActiveTab('admin-list')"
                            @click="scrollToTab($event)"
                            class="flex-shrink-0 px-4 py-2.5 rounded-lg border border-gray-200 text-sm transition-all duration-200 whitespace-nowrap
                                   {{ $activeTab === 'admin-list' ? 'bg-indigo-100 text-indigo-700 font-medium' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Liste des administrateurs</span>
                            </div>
                        </button>
                    @endcan
                </nav>
            </div>

            {{-- Flèche droite avec gradient --}}
            <div
                x-show="showRightArrow"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute right-0 top-0 bottom-0 z-10 flex items-center justify-end pointer-events-none p-2"
                style="background: linear-gradient(to left, rgba(249, 250, 251, 1) 20%, rgba(249, 250, 251, 0) 100%); aspect-ratio: 1/1;"
            >
                <button
                    @click="scrollRight()"
                    class="pointer-events-auto mr-1 w-7 h-7 bg-white rounded-full shadow-md border border-gray-200 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Desktop: Tabs Navigation (hidden on mobile) --}}
    <div class="hidden sm:block border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            {{-- Profil Tab --}}
            <button
                wire:click="setActiveTab('profile')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'profile' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Mon profil
                </div>
            </button>

            {{-- Mot de passe Tab --}}
            <button
                wire:click="setActiveTab('password')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'password' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Mot de passe
            </div>
        </button>

        {{-- Créer admin Tab (Super-admin only) --}}
        @can('createUser', App\Models\User::class)
            <button
                wire:click="setActiveTab('create-admin')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'create-admin' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Créer un administrateur
                </div>
            </button>

            {{-- Liste admin Tab (Super-admin only) --}}
            <button
                wire:click="setActiveTab('admin-list')"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors
                       {{ $activeTab === 'admin-list' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Liste des administrateurs
                </div>
            </button>
        @endcan
    </nav>
</div>

{{-- Tabs Content (chargement dynamique selon onglet actif) --}}
<div class="mt-6">
    @if($activeTab === 'profile')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            @livewire('admin.settings.profile-update.profile-update-form')
        </div>
    @elseif($activeTab === 'password')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            @livewire('admin.settings.password-update.password-update-form')
        </div>
    @elseif($activeTab === 'create-admin')
        @can('createUser', App\Models\User::class)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @livewire('admin.settings.user-create.user-create-form')
            </div>
        @endcan
    @elseif($activeTab === 'admin-list')
        @can('createUser', App\Models\User::class)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @include('admin.settings.partials.admin-list')
            </div>
        @endcan
    @endif
</div>
</div>
