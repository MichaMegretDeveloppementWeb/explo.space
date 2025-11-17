<div class="max-w-7xl mx-auto px-0 sm:px-6 lg:px-8 py-8"
     x-data="{
         activeTab: $wire.entangle('activeTranslationTab'),
         scrollToFirstError() {
             this.$nextTick(() => {
                 setTimeout(() => {
                     const firstError = document.querySelector('.error-message');
                     if (firstError && firstError.offsetParent !== null) {
                         const navHeight = document.querySelector('body>nav')?.offsetHeight || 0;
                         const offset = navHeight + 150;
                         const elementPosition = firstError.getBoundingClientRect().top;
                         const offsetPosition = elementPosition + window.pageYOffset - offset;
                         window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                     }
                 }, 150);
             });
         }
     }"
     @scroll-to-validation-error.window="scrollToFirstError()">

    @include('livewire.admin.place.store.partials.form-header')

    {{-- Message informatif pour EditRequest --}}
    @if($editRequestId)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Édition suite à une demande de modification
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p class="py-1">
                            Ce formulaire contient les modifications proposées par un visiteur. Les champs modifiés sont identifiés et affichent l'ancienne valeur pour faciliter la comparaison.
                        </p>
                        <p class="py-1">
                            Pensez à appliquer la traduction pour les champs modifiés si nécessaire.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="save" novalidate class="space-y-6">

        {{-- Section 2: Traductions --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Traductions</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Renseignez les informations dans chaque langue
                </p>
            </div>

            <div>
                @include('livewire.admin.place.store.partials.translation-tabs')
            </div>
        </div>

        {{-- Section 1: Localisation --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Localisation</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Définissez la position géographique du lieu
                </p>
            </div>

            <div class="p-6">
                @include('livewire.admin.place.store.partials.location-selector')
            </div>
        </div>

        {{-- Section 3: Photos --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Photos</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Ajoutez jusqu'à 10 photos (max 10 Mo par photo)
                </p>
            </div>

            <div class="p-6">
                @include('livewire.admin.place.store.partials.photo-gallery.index')
            </div>
        </div>

        {{-- Section 4: Catégories et Tags --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Classification</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Assignez des catégories et tags pour organiser le lieu
                </p>
            </div>

            <div class="p-6 space-y-6">
                @include('livewire.admin.place.store.partials.categories-selector')
                @include('livewire.admin.place.store.partials.tags-selector')
            </div>
        </div>

        {{-- Section 5: Paramètres --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Paramètres</h2>
            </div>

            @include('livewire.admin.place.store.partials.settings-section')

        </div>


        @include('livewire.admin.place.store.partials.form-actions')
    </form>
</div>
