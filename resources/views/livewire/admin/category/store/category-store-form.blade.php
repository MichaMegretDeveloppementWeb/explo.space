<div class="max-w-5xl mx-auto px-0 sm:px-6 lg:px-8 py-8"
     x-data="{
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

    @include('livewire.admin.category.store.partials.form-header')

    <form wire:submit="save" novalidate class="space-y-6">

        {{-- Section: Informations de base --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Informations de base</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Nom et description de la catégorie
                </p>
            </div>

            <div class="p-6 space-y-6">
                @include('livewire.admin.category.store.partials.basic-info-section')
            </div>
        </div>

        {{-- Section: Paramètres --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h2 class="text-base font-semibold text-gray-900">Paramètres</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Définissez la couleur et l'état de la catégorie
                </p>
            </div>

            <div class="p-6 space-y-6">
                @include('livewire.admin.category.store.partials.settings-section')
            </div>
        </div>

        @include('livewire.admin.category.store.partials.form-actions')
    </form>

    {{-- Delete Confirmation Modal --}}
    @if($mode === 'edit')
        @include('livewire.admin.category.store.partials.delete-modal')
    @endif
</div>
