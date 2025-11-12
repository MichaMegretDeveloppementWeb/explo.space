<!-- Section Formulaire de contact -->
<section class="bg-white py-12 sm:py-16 md:py-20 lg:py-24">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Title -->
        <div class="text-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                {{ __('web/pages/contact.form.title') }}
            </h2>
            <p class="text-base text-gray-600">
                {{ __('web/pages/contact.form.subtitle') }}
            </p>
        </div>

        <!-- Livewire Contact Form Component -->
        <div class="bg-gray-50 rounded-xl p-6 sm:p-8 shadow-sm">
            @livewire('web.contact.contact-form')
        </div>

    </div>
</section>
