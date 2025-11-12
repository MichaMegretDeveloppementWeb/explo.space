<div class="max-w-5xl mx-auto px-0 sm:px-6 lg:px-8 py-8"
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

    @include('livewire.admin.tag.store.partials.form-header')

    <form wire:submit="save" novalidate class="space-y-6">

        {{-- Section: Traductions --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Traductions</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Renseignez les informations dans chaque langue
                </p>
            </div>

            <div>
                @include('livewire.admin.tag.store.partials.translation-tabs')
            </div>
        </div>

        {{-- Section: Paramètres --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Paramètres</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Définissez la couleur et l'état du tag
                </p>
            </div>

            <div class="p-6 space-y-6">
                @include('livewire.admin.tag.store.partials.settings-section')
            </div>
        </div>

        @include('livewire.admin.tag.store.partials.form-actions')
    </form>

    {{-- Translation Confirmation Modal --}}
    @include('livewire.admin.tag.store.partials.translation-modal')

    {{-- Delete Confirmation Modal --}}
    @if($mode === 'edit')
        @include('livewire.admin.tag.store.partials.delete-modal')
    @endif
</div>
