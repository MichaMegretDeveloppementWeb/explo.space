<div class="relative"
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
    <form id="placeRequestForm"
          class="space-y-6"
          x-data="placeRequestForm"
          @submit.prevent="handleSubmit">

        <div class="bg-white rounded-lg shadow-lg p-6">
            {{-- Contact Section --}}
            @include('livewire.web.place.place-request.partials.contact-section')
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            {{-- Place Information Section --}}
            @include('livewire.web.place.place-request.partials.place-info-section')
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            {{-- Location Section --}}
            @include('livewire.web.place.place-request.partials.location-section')
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            {{-- Photos Section --}}
            @include('livewire.web.place.place-request.partials.photos-section')
        </div>

        {{-- Form Actions (with reCAPTCHA and error displays) --}}
        @include('livewire.web.place.place-request.partials.form-actions')

    </form>
</div>
